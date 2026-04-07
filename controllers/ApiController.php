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
            if(!isset($data['signatures']) or !$data['signatures']) {
                $this->api->addError('Не указаны подписи');
                return false;
            }
            return [
                'document_id' => $data['document_id'],
                'signatures' => $this->getImageData($data['signatures']),
                'custom' => $data['custom'],
            ];
        }
        return false;

        // TEST
        /*
        $signature_1 = $this->renderPartial('//document/_signature_1');
        $signature_2 = $this->renderPartial('//document/_signature_2');
        $signature_3 = $this->renderPartial('//document/_signature_3');
        return [
            'document_id' => 2,
            'signatures' => [
                'signature_1' => $signature_1,
                'signature_2' => $signature_2,
                'signature_3' => $signature_3,
                'signature_4' => $signature_2,
            ],
        ];
        */
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

        if ($number && $patientId && $appointmentId && $paymentMode) {

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
        \Yii::$app->infoLog->add('result', $this->api->result, 'signatures-log.txt');
        return $this->api->result;
    }




}
