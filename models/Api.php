<?php

namespace app\models;

use yii\base\Model;
use app\models\Setting;

class Api extends Model
{
    private $request_url;
    private $api_key;

    private $time_start;
    private $time_end;
    private $time_now;
    private $status_id = '2,3';
    public $appointment_id = '';
    //public $doctor_id = 13256;
    public $doctor_id = null;

    const STATUS_ID_WRITED = 1;     // записан
    const STATUS_ID_WAIT   = 2;     // ожидает
    const STATUS_ID_BUSY   = 3;     // на приеме  upcoming

    public $_session_id;            // если меняется, то проверяем можно ли показать какое-то объявление


    public function __construct()
    {
        $this->request_url = Setting::findOne(['key' => 'rnova_api_url'])->value;
        $this->api_key = Setting::findOne(['key' => 'rnova_api_key'])->value;
        $this->setSessionId();
        date_default_timezone_set('Europe/Moscow');
        $this->time_start = date('d.m.Y').' 00:00';
        $this->time_end = date('d.m.Y').' 23:59';
        $this->time_now = date('d.m.Y H:i');
    }

    protected function setSessionId()
    {
        $session = \Yii::$app->session;
        if(!$session->has('session_id')) {
            $session_id = mt_rand(100000,1000000);
            $session->set('session_id', $session_id);
        }
        $this->_session_id = $session->get('session_id');
    }




    public function getTimeStart()
    {
        return $this->time_start;
    }
    public function getTimeEnd()
    {
        return $this->time_end;
    }
    public function getTimeNow()
    {
        return $this->time_now;
    }
    public function getDate()
    {
        return date('d.m.Y', strtotime($this->time_start));
    }

    public function getClinics()
    {
        $data = $this->request('getClinics');
        return $data;
    }

    public function getAppointments($params = [])
    {
        $data = $this->request('getAppointments', $params);
        return $data;
    }

    public function getPatient($params = [])
    {
        $data = $this->request('getPatient', $params);
        return $data;
    }

    public function uploadFile($params = [])
    {
        $data = $this->request('uploadFile', $params);
        return $data;
    }


















    private function getFullUrl($method, $params)
    {
        // Rnova
        $url = $this->request_url . $method;



        //$url = $this->request_url . '?method='.$method;
        /*if($params) {
            foreach($params as $paramName => $paramValue) {
                $url .= "&{$paramName}={$paramValue}";
            }
        }*/
        return $url;
    }

    private function request($method = 'getAppointments', $params = [])
    {
        $url = $this->getFullUrl($method, $params);

        $curl = curl_init();
        $build_query = http_build_query(array_merge(['api_key' => $this->api_key], $params));
        //$build_query = http_build_query($params);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,

            // Rnova
            //CURLOPT_POSTFIELDS => $build_query,
            //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            // Rnova
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $build_query,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            /*CURLOPT_HTTPHEADER => array(
                'Authorization: Basic a3V0c2FldmEuZGFyaWFfYXBpMS5nbWFpbC5jb206Rk03WDJGOVJTWFRFV0w3NQ==',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: PHPSESSID=eglk0r28e3ccle8gg03ob9igs2'
            ),*/
        ));

        $response = curl_exec($curl);

        $info = curl_getinfo($curl);

        if($info['http_code'] != 200) {
            //file_put_contents('curl-logs.txt', date('d.m.Y H:i:s').' - '.print_r($info, true)."\n", FILE_APPEND);
        }
        curl_close($curl);
        return $response;
    }





}
