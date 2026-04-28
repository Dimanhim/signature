<?php

namespace app\models;

use app\components\ApiHelper;
use app\models\Document;
use app\models\Setting;
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

        \Yii::$app->infoLog->add('tablet_id', $tablet_id, date('Y-m-d-H') . '_documents.txt');

        $data = [];
        if(!$tablet_id) {
            $this->addError(self::ERROR_TABLET_EMPTY);
            return $this->result;
        }
        if(!$tablet = Tablet::findOne($tablet_id)) {
            \Yii::$app->infoLog->add('', 'планшет не найден', date('Y-m-d-H') . '_documents.txt');
            $this->addError(self::ERROR_TABLET_NOT_FOUND);
            return $this->result;
        }

        if($documents = Document::findModels()->andWhere(['tablet_id' => $tablet->id])->andWhere(['canceled' => 0])->andWhere(['is_signature' => NULL])->all()) {
            \Yii::$app->infoLog->add('найдено документов - ', count($documents), date('Y-m-d-H') . '_documents.txt');

            foreach($documents as $document) {

                $sigs = $document->signatures;
                \Yii::$app->infoLog->add('DEBUG: count sigs', ['count' => count($sigs), 'doc_id' => $document->id], date('Y-m-d-H') . '_documents.txt');

                if (empty($sigs)) {
                    \Yii::$app->infoLog->add('документ не подписан attrs', $document->attributes, date('Y-m-d-H') . '_documents.txt');
                    $data[] = $document->contentResponse();
                }
            }
        }
        if($data) {
            $this->result['data'] = $data;
        }

        \Yii::$app->infoLog->add('result', $this->result, date('Y-m-d-H') . '_documents.txt');

        $this->getAppointment();
        $this->getInvoices();

        \Yii::$app->infoLog->add('total result', $this->result, date('Y-m-d-H') . '_documents.txt');
    }

    public function getAppointment()
    {
        $appointmentId = $this->result['data'][0]['appointment_id'] ?? null;

        if(!$appointmentId) return false;

        $response = Yii::$app->api->getAppointments(['appointment_id' => $appointmentId]);

        \Yii::$app->infoLog->add('getAppointments appointmentId', $appointmentId, date('Y-m-d-H') . '_documents.txt');
        \Yii::$app->infoLog->add('getAppointments response', $response, date('Y-m-d-H') . '_documents.txt');
        $data = ApiHelper::getDataFromApi($response);

        $this->result['appointment'] = $data[0] ?? null;
    }

    public function getInvoices()
    {
        $services = $this->result['appointment']['services'] ?? null;
        if(!$services) return false;

        $invoiceNumbers = [];
        foreach($services as $service) {
            if($service['invoice_number']) {
                $invoiceNumbers[] = $service['invoice_number'];
            }
        }
        if(!$invoiceNumbers) {
            $this->result['invoices'] = [];
            return false;
        }
        $invoices = Yii::$app->api->getInvoices(['number' => $invoiceNumbers, 'date_from' => '01.01.2000', 'date_to' => '01.01.2050']);
        \Yii::$app->infoLog->add('getInvoices response', $invoices, date('Y-m-d-H') . '_documents.txt');
        $allInvoices = ApiHelper::getDataFromApi($invoices) ?: [];

        // НОРМАЛИЗАЦИЯ: если пришел один счет как объект, оборачиваем его в массив
        $invoicesList = (isset($allInvoices['number'])) ? [$allInvoices] : $allInvoices;

        if(!$invoicesList && !is_array($allInvoices)) {
            $this->result['invoices'] = [];
            return false;
        }
        $unpaid = [];
        foreach ($invoicesList as $inv) {
            if (isset($inv['status_code']) && (int)$inv['status_code'] !== 2) {
                $unpaid[] = $inv;
            }
        }

        $this->result['invoices'] = $unpaid;
    }

    public function cancelDocument()
    {
        $params = Yii::$app->request->post();
        $documentId = $params['document_id'] ?? null;
        if(!$documentId) {
            $this->addError('Произошла ошибка, попробуйте позднее');
            return $this->result;
        }
        $document = Document::findOne($documentId);
        if(!$document) {
            $this->addError('Документ не найден, попробуйте позднее');
            return $this->result;
        }
        $cancel = $document->cancelDocument();
        if(!$cancel) {
            $this->addError('Ошибка отмены документа');
            return $this->result;
        }
        return $this->result;
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
        \Yii::$app->infoLog->add('setSignatures', '');
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
            \Yii::$app->infoLog->add('1', 'here');
            $document->setContentWithCustom($data);
            \Yii::$app->infoLog->add('2', 'here');
            if(isset($data['signatures'])) {
                \Yii::$app->infoLog->add('3', 'here');
                $document->contentWithSignatures($data['signatures']);
                \Yii::$app->infoLog->add('4', 'here');
                $document->contentWithPatterns($data);
                \Yii::$app->infoLog->add('5', 'here');
                $document->generatePdf();
                \Yii::$app->infoLog->add('6', 'here');
                $document->uploadFile();
                \Yii::$app->infoLog->add('7', 'here');
                \Yii::$app->infoLog->add('getErrorsMessage', $document->getErrorsMessage());
                if(!$document->hasDocumentErrors()) {
                    \Yii::$app->infoLog->add('8', 'here');
                    if($document->saveSignatures($data['signatures'])) {
                        \Yii::$app->infoLog->add('9', 'here');
                        $this->result['message'] = 'Успешно добавлено '.count($data['signatures']).' подписей, документ отправлен';
                    }
                }
                else {
                    \Yii::$app->infoLog->add('10', 'here');
                    $this->addError($document->getErrorsMessage());
                }
            }
        }
    }












    public function hasErrors()
    {
        return $this->result['error'];
    }
    public function addError($errorMessage)
    {
        $this->result['error'] = 1;
        $this->result['error_message'] = $errorMessage;
        $this->result['data'] = [];
    }
}
