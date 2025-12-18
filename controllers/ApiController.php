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
        $imgWidth = 240;
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
        \Yii::$app->infoLog->add('data', $data, 'signatures-log.txt');
        if($data) {
            $this->api->setSignatures($data);
        }
        return $this->responseValue();
    }







    private function responseValue()
    {
        // if($this->api->hasErrors()) {
        //     \Yii::$app->infoLog->add('error_message', $this->api->result['error_message'], 'api-logs.txt');
        // }
        \Yii::$app->infoLog->add('result', $this->api->result, 'signatures-log.txt');
        return $this->api->result;
    }




}
