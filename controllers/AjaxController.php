<?php

namespace app\controllers;

use app\models\Document;
use app\models\Industry;
use Yii;
use app\models\Company;
use app\models\User;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class AjaxController extends Controller
{
    public $res = ['result' => 0, 'message' => null, 'html' => null];
    /**
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionShowAppointment()
    {
        if(!$appointment_id = Yii::$app->request->post('appointment_id')) return $this->res;
        $model = new Document(['appointment_id' => $appointment_id]);
        $appointment = $model->getAppointment(false);
        if(!$appointment) {
            $this->res['message'] = $model->getAppointmentErrorMessage();
            return $this->res;
        }
        if($patient = $model->getPatient(false)) {
            $patientName = $patient['patient_name'];
            $patientBirthDate = $patient['patient_birthdate'];
            $patientMessage = $patientName.', '.$patientBirthDate.' г.р.';
            $this->res['html'] = $model->getAppointmentSuccessMessage($patientMessage);
            $this->res['result'] = 1;
            return $this->res;
        }
        $this->res['message'] = $model->getAppointmentErrorMessage();
        return $this->res;
    }
}
