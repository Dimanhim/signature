<?php

namespace app\models\traits;

use Yii;
use app\components\ApiHelper;

trait AppointmentTrait
{
    /**
     * @return mixed|null
     */
    public function setAppointment()
    {
        $params = [
            'appointment_id' => $this->appointment_id,
        ];
        $request = Yii::$app->api->getAppointments($params);

        $data = ApiHelper::getDataFromApi($request);

        $this->appointment = $data[0] ?? null;

        return $this->appointment;
    }

    /**
     * @return mixed
     */
    public function setAppointmentCustom()
    {
        if($this->appointment) {
            $this->appointment['appointment_id'] = $this->appointment['id'] ?? null;
            $this->appointment['visit_date'] = isset($this->appointment['time_start']) ? date('d.m.Y', strtotime($this->appointment['time_start']))  : '';
            $this->appointment['time_from'] = isset($this->appointment['time_start']) ? date('H:i', strtotime($this->appointment['time_start']))  : '';
            $this->appointment['services_no_price'] = $this->getServicesNoPriceFromData();
            $this->appointment['service_list'] = $this->getServicesListFromData();
            $this->appointment['service_list_day'] = $this->getServicesListDayFromData();
            $this->appointment['price_full'] = $this->getPriceFullFromData();
            $this->appointment['user_name_short'] = $this->appointment['author_name'];
        }

        return $this->appointment;
    }
}