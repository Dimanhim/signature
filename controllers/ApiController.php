<?php

namespace app\controllers;

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
