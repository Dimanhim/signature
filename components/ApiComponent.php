<?php

namespace app\components;

use app\components\BaseComponent;
use yii\base\Component;
use app\components\RnovaApi;
use app\models\Setting;

class ApiComponent extends BaseComponent
{
    private $api;

    const STATUS_ID_WRITED = 1;     // записан
    const STATUS_ID_WAIT   = 2;     // ожидает
    const STATUS_ID_BUSY   = 3;     // на приеме  upcoming

    /**
     *
     */
    public function init()
    {
        $request_url = Setting::findOne(['key' => 'rnova_api_url'])->value;
        $api_key = Setting::findOne(['key' => 'rnova_api_key'])->value;
        $this->api = new RnovaApi($request_url, $api_key);
        return parent::init();
    }

    public function getClinics()
    {
        return $this->api->getRequest('getClinics');
    }

    public function getAppointments($params = [])
    {
        return $this->api->getRequest('getAppointments', $params);
    }
    public function getAppointmentServices($params = [])
    {
        return $this->api->getRequest('v2/getAppointmentServices', $params);
    }

    public function getInvoices($params = [])
    {
        return $this->api->getRequest('v2/getInvoices', $params);
    }

    public function getInvoiceServices($params = [])
    {
        return $this->api->getRequest('v2/getInvoiceServices', $params);
    }

    public function getUsers($params = [])
    {
        return $this->api->getRequest('getUsers', $params);
    }

    public function getServices($params = [])
    {
        return $this->api->getRequest('getServices', $params);
    }

    public function getPatient($params = [])
    {
        return $this->api->getRequest('getPatient', $params);
    }

    public function confirmAppointment($params = [])
    {
        return $this->api->getRequest('confirmAppointment', $params);
    }

    public function cancelAppointment($params = [])
    {
        return $this->api->getRequest('cancelAppointment', $params);
    }

    public function uploadFile($params = [])
    {
        return $this->api->getRequest('uploadFile', $params);
    }
}
