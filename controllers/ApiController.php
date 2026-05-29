<?php

namespace app\controllers;

use app\components\ApiHelper;
use app\models\Api;
use app\models\ApiResponse;
use app\models\Company;
use app\models\Document;
use app\models\Payment;
use Yii;
use app\components\Helper;
use yii\filters\ContentNegotiator;
use yii\helpers\Html;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\Cors;

/**
 *
 */
class ApiController extends Controller
{
    private $api;

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'corsFilter' => [
                'class' => Cors::class,
            ],
        ];
    }

    // стопудова нужен этот метод
    public function beforeAction($action)
    {
        $this->api = new ApiResponse();
        return parent::beforeAction($action);
    }

    public function getImageData($signatures)
    {
        $imgWidth = Document::SIGNATURE_WIDTH;
        $data = [];
        if($signatures) {
            foreach($signatures as $signatureID => $signatureSrc) {
                $data[$signatureID] = "<img src='{$signatureSrc}' width='{$imgWidth}' alt='' />";
            }
        }
        return $data;
    }

    public function getRequestParams()
    {
        $dataJson = \Yii::$app->request->getRawBody();
        if(($data = json_decode($dataJson, true))) {
            if(!isset($data['document_id']) or !$data['document_id']) {
                $this->api->addError('Не указан документ');
                return false;
            }

            // ПРОВЕРКА НА ПОДПИСИ:
            // Если функционал оплаты полностью отключен в админке, подписи обязательны всегда.
            // Если включен — бэкенд разрешит пустой массив подписей.
            $isPaymentOn = (bool)\Yii::$app->settings->getParam('payment_functional');

            if (!$isPaymentOn) {
                if(!isset($data['signatures']) or !$data['signatures']) {
                    $this->api->addError('Не указаны подписи');
                    return false;
                }
            }

            return [
                'document_id' => $data['document_id'],
                'signatures' => isset($data['signatures']) ? $this->getImageData($data['signatures']) : [],
                'custom' => $data['custom'] ?? [],
            ];
        }
        return false;
    }

    public function actionGetDocuments()
    {
        $this->api->getDocuments();
//        \Yii::$app->infoLog->add('documents', $this->api->result['data'][0], 'document.txt');
        return $this->responseValue();
    }

    public function actionGetPaymentLink()
    {
        $params = Yii::$app->request->post() ?: Yii::$app->request->get();
        $number = $params['number'] ?? null;
        $patientId = $params['patient_id'] ?? null;
        $appointmentId = $params['appointment_id'] ?? null;
        $paymentMode = $params['payment_mode'] ?? null;

        \Yii::$app->infoLog->daily('actionGetPaymentLink params', $params, 'payment-check');

        if ($number && $patientId && $appointmentId && $paymentMode) {

            // 1. ПРОВЕРКА НА ДУБЛИКАТ:
            // Ищем существующую запись по счету и визиту, которая еще не оплачена
            $payment = Payment::find()
                ->where([
                    'appointment_id' => $appointmentId,
                    'invoice_number' => (string)$number,
                ])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if ($payment) {
                if ($payment->is_payed == 2) {
                    // Если уже оплачено, просто говорим фронту — платить не надо
                    $this->api->addMessage('Счет уже оплачен');
                    return $this->responseValue();
                }
                // Если висит неоплаченный (0 или 1), отдаем старую ссылку
                $this->api->result['qr_link'] = $payment->payment_link;
                return $this->responseValue();
            }

            $qrLink = null;
            try {
                $request = Yii::$app->api->getPaymentLink([
                    'number' => $number,
                    'patient_id' => $patientId,
                    'appointment_id' => $appointmentId,
                    'payment_mode' => $paymentMode
                ]);

                \Yii::$app->infoLog->daily('paymentLink request', $request, 'payment-check');

                // Если МИС вернула ошибку авторизации (например, 401) или пустой ответ
                if (isset($request['data']['code']) && (int)$request['data']['code'] === 401) {
                    // ИМИТИРУЕМ ТЕСТОВУЮ ССЫЛКУ ДЛЯ ОПЛАТЫ
                    $qrLink = 'https://nspk.ru';
                    \Yii::$app->infoLog->daily('MOCK_LINK_ACTIVATED', 'МИС вернула 401. Сгенерирована тестовая ссылка.', 'payment-check');
                } else {
                    $qrLink = ApiHelper::getDataFromApi($request);
                }
            } catch (\Exception $e) {
                // Если МИС физически недоступна (сетевая ошибка), активируем заглушку
                $qrLink = 'https://nspk.ru';
                \Yii::$app->infoLog->daily('MOCK_LINK_ACTIVATED_ON_EXCEPTION', $e->getMessage(), 'payment-check');
            }

            \Yii::$app->infoLog->daily('QR LINK', $qrLink, 'payment-check');

            // 3. СОХРАНЕНИЕ И СИНХРОНИЗАЦИЯ С ЛОКАЛЬНОЙ БД
            if ($qrLink) {
                $payment = new \app\models\Payment();
                $payment->appointment_id = $appointmentId;
                $payment->invoice_number = (string)$number;
                $payment->patient_id = $patientId;
                $payment->payment_link = $qrLink;
                $payment->is_payed = 0; // Изначально счет НЕ оплачен

                if ($payment->save()) {
                    $this->api->result['qr_link'] = $payment->payment_link;
                    return $this->responseValue();
                } else {
                    $this->api->addError('Ошибка сохранения данных платежа');
                    return $this->responseValue();
                }
            }

            $this->api->addError('Не удалось получить ссылку от платежного сервиса');
            return $this->responseValue();
        }

        $this->api->addError('Не указаны обязательные параметры для получения ссылки на оплату');
        return $this->responseValue();
    }

    public function actionCheckPaymentStatus()
    {
        $params = Yii::$app->request->post() ?: Yii::$app->request->get();
        $appointmentId = $params['appointment_id'] ?? null;
        $invoiceNumberLocal = $params['number'] ?? null; // Номер, который у нас был изначально
        $patientId = $params['patient_id'] ?? null;
        $invoiceNumber = $params['number'] ?? null;

        if (!$appointmentId || !$patientId) {
            $this->api->addError('Не указаны ID визита или пациента');
            return $this->responseValue();
        }

        // 1. Тянем ВСЕ счета за сегодня
        $today = date('d.m.Y');

        \Yii::$app->infoLog->daily('patient_id', $patientId, 'payment-check');


        $response = Yii::$app->api->getInvoices([
            'patient_id' => $patientId,
            'date_from' => date('d.m.Y', strtotime('-30 days')),
            'date_to' => date('d.m.Y', strtotime('+1 day'))
        ]);

        \Yii::$app->infoLog->daily('bool response', $response, 'payment-check');


        $data = ApiHelper::getDataFromApi($response) ?: [];

        \Yii::$app->infoLog->daily('DEBUG DATA', [
            'data' => $data,
        ], 'payment-check');

        // Нормализация в массив, если пришел один счет
        $allInvoices = (isset($data['number'])) ? [$data] : $data;

        /**
        ТЕСТОВАЯ ОПЛАТА
         */
//        $allInvoices = [
//            [
//                'appointment_id' => (int)$appointmentId,
//                'number' => $invoiceNumber ?: 'TEST-INVOICE-001',
//                'status_code' => 2 // Имитируем успешную оплату (2)
//            ]
//        ];


        $foundStatus = 0;
        $realNumber = null;

        \Yii::$app->infoLog->daily('DEBUG ALL INVOICES', [
            'allInvoices' => $allInvoices,
        ], 'payment-check');


        // 2. Ищем нужный счет по appointment_id
        foreach ($allInvoices as $inv) {
            if (isset($inv['appointment_id']) && (int)$inv['appointment_id'] === (int)$appointmentId) {
                $foundStatus = (int)($inv['status_code'] ?? 0);
                $realNumber = (string)($inv['number'] ?? null);
                break;
            }
        }

        // 3. Обновляем нашу таблицу payments
        $localPayment = Payment::find()
            ->where(['appointment_id' => $appointmentId])
            //->andWhere(['invoice_number' => (string)$invoiceNumberLocal])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        \Yii::$app->infoLog->daily('DEBUG local PAYMENT', [
            '$localPayment' => $localPayment->attributes,
        ], 'payment-check');

        if ($localPayment) {
            $localPayment->is_payed = $foundStatus;
            if ($realNumber) {
                $localPayment->invoice_number_real = $realNumber;
            }
            if (!$localPayment->save()) {
                // Добавь этот лог, чтобы увидеть, не спотыкается ли сохранение
                \Yii::$app->infoLog->daily('SAVE_ERROR', $localPayment->getErrors(), 'payment-check');
            }
        }

        // ЛОГ РЕЗУЛЬТАТА (поможет понять, что уходит на фронт)
        \Yii::$app->infoLog->daily('RESULT', [
            'appointment_id' => $appointmentId,
            'status_to_front' => $foundStatus
        ], 'payment-check');

        $this->api->result['is_payed'] = $foundStatus;

        return $this->responseValue();
    }

    public function actionGetSettings()
    {
        $this->api->getSettings();
        return $this->responseValue();
    }


    public function actionSetSignatures()
    {
        $data = $this->getRequestParams();
        if($data) {
            $this->api->setContent($data);
        }
        return $this->responseValue();
    }

    public function actionSaveSignature()
    {
        $signatureData = Yii::$app->request->post('signature');

        if (!$signatureData) {
            $this->api->addError('Данные подписи не переданы');
            return $this->responseValue();
        }

        $userId = Yii::$app->user->id;
        if (!$userId) {
            $this->api->addError('Пользователь не авторизован');
            return $this->responseValue();
        }

        $model = \app\models\UserSignature::findOne(['user_id' => $userId])
            ?: new \app\models\UserSignature(['user_id' => $userId]);

        $model->signature_data = $signatureData;
        $model->is_active = 1;

        if ($model->save()) {
            Yii::$app->settings->setSignature();

            $this->api->result['message'] = 'Образец подписи успешно сохранен';
            $this->api->result['error'] = 0;
        } else {
            $this->api->addError('Ошибка сохранения: ' . implode(', ', $model->getFirstErrors()));
        }

        return $this->responseValue();
    }








    private function responseValue()
    {
//         if($this->api->hasErrors()) {
//             \Yii::$app->infoLog->add('error_message', $this->api->result['error_message'], 'api-logs.txt');
//         }
        \Yii::$app->infoLog->add('result', $this->api->result, 'signatures-log.txt');
        return $this->api->result;
    }




}
