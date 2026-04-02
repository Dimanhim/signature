<?php

namespace app\models\traits;

use Yii;
use app\components\ApiHelper;

trait ClinicTrait
{
    /**
     * @return bool|mixed|null
     */
    public function setClinics()
    {
        $request = Yii::$app->api->getClinics();

        if(!$request) return null;

        $this->clinics = ApiHelper::getDataFromApi($request);

        return $this->clinics;
    }

    /**
     * @param $clinicId
     * @return void|null
     */
    public function setClinic($clinicId = null)
    {
        $this->setClinics();

        if(!$this->clinics) return null;

        if(!$clinicId) $clinicId = $this->appointment['clinic_id'] ?? null;

        foreach($this->clinics as $clinic) {
            if($clinic['id'] == $clinicId) {
                $this->clinic['clinic_email'] = $clinic['email'];
                $this->clinic['clinic_phone'] = $clinic['phone'];
                $this->clinic['clinic_site'] = $clinic['site'];
                $this->clinic['clinic_bic'] = $clinic['bic'];
                $this->clinic['clinic_bank'] = $clinic['bank'];
                $this->clinic['clinic_cor_account'] = $clinic['cor_account'];
                $this->clinic['clinic_account'] = $clinic['account'];
                return $this->clinic;
            }
        }
    }
}