<?php

namespace app\controllers;

use app\components\ApiHelper;
use app\models\Api;
use app\models\ApiResponse;
use app\models\Company;
use app\models\Document;
use Yii;
use app\components\Helper;
use yii\filters\ContentNegotiator;
use yii\helpers\Html;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\Cors;
use app\models\Payment;

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
        $imgWidth = 300;
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
//            if(!isset($data['signatures']) or !$data['signatures']) {
//                $this->api->addError('Не указаны подписи');
//                return false;
//            }
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

        \Yii::$app->infoLog->add('actionGetPaymentLink params', $params);

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
                    $this->api->addError('Счет уже оплачен');
                    return $this->responseValue();
                }
                // Если висит неоплаченный (0 или 1), отдаем старую ссылку
                $this->api->result['qr_link'] = $payment->payment_link;
                return $this->responseValue();
            }

            // 3. Если записи нет — запрашиваем новую ссылку у МИС
            $request = Yii::$app->api->getPaymentLink([
                'number' => $number,
                'patient_id' => $patientId,
                'appointment_id' => $appointmentId,
                'payment_mode' => $paymentMode
            ]);

            $qrLink = ApiHelper::getDataFromApi($request);

            if ($qrLink) {
                $payment = new Payment();
                $payment->appointment_id = $appointmentId;
                $payment->invoice_number = (string)$number;
                $payment->patient_id = $patientId;
                $payment->payment_link = $qrLink;
                $payment->is_payed = 0;

                if ($payment->save()) {
                    $this->api->result['qr_link'] = $payment->payment_link;
                    return $this->responseValue();
                } else {
                    $this->api->addError('Ошибка сохранения данных платежа');
                    return $this->responseValue();
                }
            }

            \Yii::$app->infoLog->add('qr_link', $this->api->result['qr_link']);

            $this->api->addError('Не удалось получить ссылку от платежного сервиса');
            return $this->responseValue();
        }

        $this->api->addError('Не указаны обязательные параметры для получения ссылки на оплату');
        return $this->responseValue();
    }

    /**
     * Входящий вебхук оплаты
     * URL: https://docs.medcentralfa.ru/api/payment?key=olemfy5ikd6758ikdm
     */
    public function actionPayment($key)
    {
        return;
        \Yii::$app->infoLog->add('RAW_BODY', Yii::$app->request->getRawBody(), date('Y-m-d').'-payment-hook.txt');
        \Yii::$app->infoLog->add('GET_PARAMS', Yii::$app->request->get(), date('Y-m-d').'-payment-hook.txt');

        if ($key !== 'olemfy5ikd6758ikdm') {
            throw new \yii\web\ForbiddenHttpException('Invalid security key.');
        }

        $data = Yii::$app->request->bodyParams;

        \Yii::$app->infoLog->add('data', $data, date('Y-m-d').'-payment-hook.txt');

        if (!$data || !isset($data['number'])) {
            $this->api->addError('Неверный формат данных вебхука');
            return $this->responseValue();
        }

        $payment = Payment::find()
            ->where(['invoice_number' => (string)$data['number']])
            ->andWhere(['is_payed' => 0])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($payment) {
            $payment->is_payed = (int)$data['status_code'];

            if ($payment->save()) {
                $this->api->result['message'] = 'Статус оплаты ' . $payment->is_payed . ' сохранен';
            } else {
                $this->api->addError('Ошибка при сохранении статуса');
            }
        }

        return $this->responseValue();
    }

    public function actionCheckPayment()
    {
        $params = Yii::$app->request->post() ?: Yii::$app->request->get();
        $invoiceNumber = $params['number'] ?? null;

        if (!$invoiceNumber) {
            $this->api->addError('Не указан номер счета');
            return $this->responseValue();
        }

        // Ищем последнюю запись по этому счету
        $payment = Payment::find()
            ->where(['invoice_number' => (string)$invoiceNumber])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($payment) {
            // Отдаем текущий статус (0, 1 или 2)
            $this->api->result['is_payed'] = $payment->is_payed;
        } else {
            $this->api->result['is_payed'] = 0;
        }

        return $this->responseValue();
    }

    public function actionCheckPaymentStatus()
    {
        $params = Yii::$app->request->post() ?: Yii::$app->request->get();
        $appointmentId = $params['appointment_id'] ?? null;
        $invoiceNumberLocal = $params['number'] ?? null; // Номер, который у нас был изначально
        $patientId = $params['patient_id'] ?? null;



        if (!$appointmentId || !$patientId) {
            $this->api->addError('Не указаны ID визита или пациента');
            return $this->responseValue();
        }

        // 1. Тянем ВСЕ счета за сегодня
        $today = date('d.m.Y');

        \Yii::$app->infoLog->add('patient_id', $patientId, '__' . date('Y-m-d H:i:s').'-payment-check.txt');


        $response = Yii::$app->api->getInvoices([
            'patient_id' => $patientId,
            'date_from' => date('d.m.Y', strtotime('-30 days')),
            'date_to' => date('d.m.Y', strtotime('+1 day'))
        ]);

        \Yii::$app->infoLog->add('bool response', $response, '__' . date('Y-m-d H:i:s').'-payment-check.txt');


        $data = ApiHelper::getDataFromApi($response) ?: [];

        \Yii::$app->infoLog->add('DEBUG DATA', [
            'data' => $data,
        ], '__' . date('Y-m-d H:i:s').'-payment-check.txt');


        // Нормализация в массив, если пришел один счет
        $allInvoices = (isset($data['number'])) ? [$data] : $data;

        $foundStatus = 0;
        $realNumber = null;

        \Yii::$app->infoLog->add('DEBUG ALL INVOICES', [
            'allInvoices' => $allInvoices,
        ], '__' . date('Y-m-d H:i:s').'-payment-check.txt');


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

        \Yii::$app->infoLog->add('DEBUG local PAYMENT', [
            '$localPayment' => $localPayment->attributes,
        ], '__' . date('Y-m-d H:i:s').'-payment-check.txt');

        if ($localPayment) {
            $localPayment->is_payed = $foundStatus;
            if ($realNumber) {
                $localPayment->invoice_number_real = $realNumber;
            }
            if (!$localPayment->save()) {
                // Добавь этот лог, чтобы увидеть, не спотыкается ли сохранение
                \Yii::$app->infoLog->add('SAVE_ERROR', $localPayment->getErrors(), '__' . date('Y-m-d H:i:s').'-payment-check.txt');
            }
        }

        // ЛОГ РЕЗУЛЬТАТА (поможет понять, что уходит на фронт)
        \Yii::$app->infoLog->add('RESULT', [
            'appointment_id' => $appointmentId,
            'status_to_front' => $foundStatus
        ], '__' . date('Y-m-d H:i:s').'-payment-check.txt');

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

    public function actionCancelDocument()
    {
        $this->api->cancelDocument();
        return $this->responseValue();
    }







    private function responseValue()
    {
//         if($this->api->hasErrors()) {
//             \Yii::$app->infoLog->add('error_message', $this->api->result['error_message'], 'api-logs.txt');
//         }
        \Yii::$app->infoLog->add('result', $this->api->result, 'signatures_2-log.txt');
        return $this->api->result;
    }




}
