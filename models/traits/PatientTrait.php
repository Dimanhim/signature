<?php

namespace app\models\traits;

use Yii;
use app\components\ApiHelper;

trait PatientTrait
{
    /**
     * @return bool|mixed
     */
    public function setPatient()
    {
        $params = [
            'id' => $this->patient_id,
            'with_documents' => true,
        ];

        $request = Yii::$app->api->getPatient($params);
        $this->patient = ApiHelper::getDataFromApi($request);
        return $this->patient;
    }

    /**
     * @return null
     */
    public function setPatientCustom()
    {
        if(!$this->patient) return null;

        $this->patient['patient_name'] = $this->patient['last_name'].' '.$this->patient['first_name'].' '.$this->patient['third_name'];
        $this->patient['patient_address'] = ($this->patient['address'] and isset($this->patient['address']['fullAddress'])) ? $this->patient['address']['fullAddress'] : '';
        $this->patient['patient_birthdate'] = $this->patient['birth_date'];
        $this->patient['patient_number'] = $this->patient['number'];
        $this->patient['patient_email'] = $this->patient['email'];
        $this->patient['patient_phone'] = $this->patient['mobile'];
        $this->patient['patient_passport'] = ($this->patient['documents'] and $this->patient['documents']['passport']) ? $this->patient['documents']['passport'] : '';
        $this->patient['patient_insurance'] = ($this->patient['documents'] and $this->patient['documents']['insurance']) ? $this->patient['documents']['insurance'] : '';
        $this->patient['patient_cert'] = ($this->patient['documents'] and $this->patient['documents']['cert']) ? $this->patient['documents']['cert'] : '';
        $this->patient['patient_address_legal'] = ($this->patient['address'] and $this->patient['address']['fullAddress']) ? $this->patient['address']['fullAddress'] : '';
        $this->patient['patient_fact_address'] = ($this->patient['address'] and $this->patient['address']['fullAddress']) ? $this->patient['address']['fullAddress'] : '';
        $this->patient['representative_name'] = '';
        $this->patient['representative_address'] = '';
        $this->patient['representative_birthdate'] = '';
        $this->patient['representative_email'] = '';
        $this->patient['representative_phone'] = '';
        $this->patient['representative_passport'] = '';

        return $this->patient;
    }

    /**
     * @return bool|mixed|null
     */
    public function setRepresentative()
    {
        if(!$this->patient || !$this->patient['parent_id']) return null;

        sleep(1);
        $params = [
            'id' => $this->patient['parent_id'],
            'with_documents' => true,
        ];

        $data = Yii::$app->api->getPatient($params);

        $this->representative = ApiHelper::getDataFromApi($data);
        return $this->representative;
    }

    /**
     * @return null
     */
    public function setRepresentativeCustom()
    {
        if(!$this->representative) return null;

        $this->patient['representative_name'] = $this->representative['last_name'].' '.$this->representative['first_name'].' '.$this->representative['third_name'];
        $this->patient['representative_address'] = ($this->representative['address'] and isset($this->representative['address']['fullAddress'])) ? $this->representative['address']['fullAddress'] : '';
        $this->patient['representative_birthdate'] = $this->representative['birth_date'];
        $this->patient['representative_email'] = $this->representative['email'];
        $this->patient['representative_phone'] = $this->representative['mobile'];
        $this->patient['representative_passport'] = ($this->representative['documents'] and $this->representative['documents']['passport']) ? $this->representative['documents']['passport'] : '';

        return $this->patient;
    }
}