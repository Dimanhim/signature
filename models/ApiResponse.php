<?php

namespace app\models;

use app\models\Document;
use app\models\Setting;
use app\components\ApiHelper;
use Yii;

class ApiResponse
{
    const ERROR_TABLET_EMPTY         = 'Не заполнен параметр ID планшета';
    const ERROR_TABLET_NOT_FOUND     = 'Планшет не найден';

    public $result = [
        'error' => 0,
        'error_message' => null,
        'message' => null,
        'data' => [],
        'appointment' => [],
        'invoices' => [],
        'qr_link' => null,
        'is_payed' => 0,
    ];

    public function getDocuments()
    {
        $params = Yii::$app->request->post() ?: Yii::$app->request->get() ;
        $tablet_id = $params['tablet_id'] ?? null;

        $data = [];
        if(!$tablet_id) {
            $this->addError(self::ERROR_TABLET_EMPTY);
            return $this->result;
        }
        if(!$tablet = Tablet::findOne($tablet_id)) {
            $this->addError(self::ERROR_TABLET_NOT_FOUND);
            return $this->result;
        }

        if($documents = Document::findModels()->andWhere(['tablet_id' => $tablet->id])->andWhere(['canceled' => 0])->andWhere(['is_signature' => NULL])->all()) {
            foreach($documents as $document) {
                if(!$document->signatures) {
                    $data[] = $document->contentResponse();
                }
            }
        }
        if($data) {
            $this->result['data'] = $data;
        }

        // ВСТАВКА ДЛЯ ОПЛАТЫ: собираем счета, если документ найден
        $this->getAppointment();
        $this->getInvoices();
    }

    public function getSettings() {
        $settings = Setting::findAll(['available_for_api' => '1']);
        $data = [];

        foreach ($settings as $setting) {
            $data = array_merge($data, $setting->apiResponse());
        }

        if($data) {
            $this->result['data'] = $data;
        }
    }

    public function setContent($data)
    {
        // здесь отправляем емейл, если send_email установлен в true
        if(isset($data['document_id'])) {
            if(!$document = Document::findOne($data['document_id'])) {
                $this->addError('Документ не найден');
                return $this->result;
            }
            if($document->is_signature) {
                $this->addError('Документ уже подписан');
                return $this->result;
            }
            $document->setContentWithCustom($data);
            if(isset($data['signatures'])) {
                $document->contentWithSignatures($data['signatures']);
                $document->contentWithPatterns($data);
                $document->generatePdf();
                $document->uploadFile();
                if(!$document->hasDocumentErrors()) {
                    if($document->saveSignatures($data['signatures'])) {
                        $document->sendPatientEmail();
                        $document->sendPatientEmailAgreement();
                        $this->result['message'] = 'Успешно добавлено '.count($data['signatures']).' подписей, документ отправлен';
                    }
                }
                else {
                    $this->addError($document->getErrorsMessage());
                }
            }
        }
    }

    /**
     * Получить данные визита из МИС по документу
     */
    public function getAppointment()
    {
        // Если функционал оплаты отключен в настройках, ничего не делаем
        if (!(bool)\Yii::$app->settings->getParam('payment_functional')) {
            return false;
        }

        $appointmentId = $this->result['data'][0]['appointment_id'] ?? null;
        if (!$appointmentId) return false;

        try {
            $response = Yii::$app->api->getAppointments(['appointment_id' => $appointmentId]);
            $data = ApiHelper::getDataFromApi($response);
            $this->result['appointment'] = $data[0] ?? null;
        } catch (\Exception $e) {
            \Yii::$app->infoLog->add('ApiResponse getAppointment Error', $e->getMessage());
            return false;
        }
    }

    /**
     * Собрать и отфильтровать неоплаченные счета пациента
     */
    public function getInvoices()
    {
        // Если функционал оплаты отключен в настройках, ничего не делаем
        if (!(bool)\Yii::$app->settings->getParam('payment_functional')) {
            return false;
        }

        $services = $this->result['appointment']['services'] ?? null;
        if (!$services) return false;

        $invoiceNumbers = [];
        foreach ($services as $service) {
            if (!empty($service['invoice_number'])) {
                $invoiceNumbers[] = $service['invoice_number'];
            }
        }

        if (!$invoiceNumbers) {
            $this->result['invoices'] = [];
            return false;
        }

        try {
            // Запрашиваем счета в МИС с широким диапазоном дат, как в Альфе
            $invoices = Yii::$app->api->getInvoices([
                'number' => $invoiceNumbers,
                //'date_from' => '01.01.2000',
                //'date_to' => '01.01.2050'
            ]);

            $allInvoices = ApiHelper::getDataFromApi($invoices) ?: [];

            // НОРМАЛИЗАЦИЯ: если пришел один счет как объект (ассоциативный массив), оборачиваем его в индексный массив
            $invoicesList = (isset($allInvoices['number'])) ? [$allInvoices] : $allInvoices;

            if (!$invoicesList && !is_array($allInvoices)) {
                $this->result['invoices'] = [];
                return false;
            }

            // Отбираем только неоплаченные счета (статус не равен 2)
            $unpaid = [];
            foreach ($invoicesList as $inv) {
                if (isset($inv['status_code']) && (int)$inv['status_code'] !== 2) {
                    $unpaid[] = $inv;
                }
            }

            $this->result['invoices'] = $unpaid;
        } catch (\Exception $e) {
            \Yii::$app->infoLog->add('ApiResponse getInvoices Error', $e->getMessage());
            $this->result['invoices'] = [];
            return false;
        }
    }













    public function hasErrors()
    {
        return $this->result['error'];
    }
    public function addMessage($message = null)
    {
    $this->result['message'] = $message;
    }
    public function addError($errorMessage)
    {
        $this->result['error'] = 1;
        $this->result['error_message'] = $errorMessage;
        $this->result['data'] = [];
    }
}
