<?php

namespace app\models\traits;

trait CustomFields
{
    private $custom_appointment_price_full_by_day = 0;     // сумма_услуг_за_день

    public function getCustomServices()
    {
        if(!$this->invoice_services) return null;

        foreach($this->invoice_services as $invoiceId => $invoiceServices) {
            if($invoiceServices) {
                foreach($invoiceServices as $invoiceService) {
                    $this->custom_appointment_price_full_by_day += $invoiceService['value'];
                }
            }
        }
    }
    public function getCustomAppointmentPriceFullByDay()
    {
        return $this->custom_appointment_price_full_by_day;
    }
}
