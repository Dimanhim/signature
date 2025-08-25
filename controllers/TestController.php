<?php

namespace app\controllers;

use app\models\Api;
use yii\web\Controller;
use app\models\Document;

class TestController extends Controller
{
    public function actionIndex()
    {



        $document = Document::findOne(2);
        //$patientId = 324353;
        //$patient = $document->getPatient($patientId);
        //$fileName = '2_11.09.23_32895.pdf';
        $document->uploadFile();
        //\Yii::$app->infoLog->add('patient', $patient);
        //\Yii::$app->infoLog->add('document', $document);
        exit;



        $str = '{"desc":"\u041c\u0435\u0442\u043e\u0434 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d"}';
        $str_2 = '{"desc":"\u041c\u0435\u0442\u043e\u0434 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d"}';
        $str_3 = '{"code":500,"desc":"\u041d\u0435\u0432\u0435\u0440\u043d\u044b\u0439 ID \u043f\u0430\u0446\u0438\u0435\u043d\u0442\u0430"}';
        echo "<pre>";
        print_r(json_decode($str));
        echo "</pre>";
        exit;
    }
}
