<?php

namespace app\models\traits;

use app\components\Helpers;

trait CustomFields
{
    private $custom_appointment_price_full_by_day = 0;     // сумма_услуг_за_день
    private $custom_invoice_services_count = 0;            // во_оказанных_услуг
    private $custom_invoice_sum = 0;                       // сумма_счета
    private $custom_invoice_services;                      // список_оказанных_услуг

    public function getCustomServices()
    {
        if(!$this->invoice_services) return null;
        if($this->invoices) {
            foreach($this->invoices as $invoice) {
                $this->custom_invoice_sum += $invoice['value'];
            }
        }

        foreach($this->invoice_services as $invoiceId => $invoiceServices) {
            if($invoiceServices) {
                foreach($invoiceServices as $invoiceService) {
                    $this->custom_invoice_services_count += $invoiceService['count'];
                    $this->custom_invoice_services[] = $invoiceService;

                    //if (Helpers::isTimeToday($this->appointments['time_start'])) {
                    $this->custom_appointment_price_full_by_day += $invoiceService['value'];
                    //}
                }
            }
        }
    }
    public function getCustomAppointmentPriceFullByDay()
    {
        return $this->custom_appointment_price_full_by_day;
    }
    public function getCustomInvoiceServicesCount()
    {
        return $this->custom_invoice_services_count;
    }
    public function getCustomInvoiceSum()
    {
        return $this->custom_invoice_sum;
    }
    public function getCustomInvoicesServices()
    {
        $serviceList = [];
        if($this->custom_invoice_services) {

            foreach($this->custom_invoice_services as $itemService) {
                $service = $this->getServiceById($itemService['id']);
                if(!$service) continue;

                $serviceList[] = $service['code'] . ' ' . $service['title'];
            }
        }
        return implode(',', $serviceList);
    }
    public function getCustomAppointmentId()
    {
        return $this->appointments['id'] ?? null;
    }
    public function getCustomUserName()
    {
        if(!isset($this->appointments['author_id'])) return null;

        if($user = $this->getUserById($this->appointments['author_id'])) {
            return $user['name'];
        }

        return null;
    }
    private function getUserById($userId = null)
    {
        return $this->users[$userId] ?? null;
    }
    public function getServiceById($serviceId = null)
    {
        return $this->services[$serviceId] ?? null;
    }
    public function getCustomUserDocumentNumber()
    {
        if(!isset($this->appointments['author_id'])) return null;

        if($user = $this->getUserById($this->appointments['author_id'])) {
            return $user['document_number'];
        }

        return null;
    }
    public function getCustomUserDocumentDate()
    {
        if(!isset($this->appointments['author_id'])) return null;

        if($user = $this->getUserById($this->appointments['author_id'])) {
            return $user['document_date'];
        }

        return null;
    }
}
