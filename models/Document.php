<?php

namespace app\models;

use app\components\ApiHelper;
use kartik\mpdf\Pdf;
use Yii;
use yii\base\Model;
use app\models\Setting;
use yii\helpers\ArrayHelper;

class Document extends BaseModel
{
    const FILE_PATH = 'pdf/template.pdf';

    public $_avaliable_patterns = [
        'место_для_подписи',
        'email_пациента',
        'моб._телефон_пациента',
    ];

    public $documentErrors = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%documents}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'document';
    }

    /**
     * @return int
     */
    public static function typeId()
    {
        return Gallery::TYPE_DOCUMENT;
    }

    public static function modelName()
    {
        return 'Документ';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['appointment_id', 'template_id', 'tablet_id'], 'required'],
            [['appointment_id', 'template_id', 'tablet_id', 'patient_id', 'user_id', 'canceled'], 'integer'],
            [['patient_name', 'patient_birthday', 'document_name'], 'string', 'max' => 255],
            [['content', 'full_content'], 'string'],
            [['is_signature'], 'safe'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'appointment_id' => 'Номер визита',
            'template_id' => 'Шаблон документа',
            'tablet_id' => 'Планшет',
            'content' => 'Html-контент документа',
            'full_content' => 'Весь html-контент',
            'patient_name' => 'ФИО пациента',
            'patient_birthday' => 'Дата рождения пациента',
            'document_name' => 'PDF документ',
            'patient_id' => 'Пациент',
            'is_signature' => 'Подписан',
            'user_id' => 'Пользователь',
            'canceled' => 'Отменен',
        ]);
    }

    public function init()
    {
        parent::init();
    }

    public function afterFind()
    {
        $this->custom_params = json_decode($this->custom_params, true) ?: [];

        return parent::afterFind();
    }

    public function beforeSave($insert)
    {
        if (!$this->user_id) {
            $this->user_id = \Yii::$app->user->id;
        }

        if (is_array($this->custom_params)) {
            $this->custom_params = json_encode($this->custom_params);
        }

        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        if($this->document_name) {
            $filePath = \Yii::getAlias('@app/web/').'pdf/'.$this->document_name;
            if(file_exists($filePath)) {
                unlink($filePath);
            }
        }
        return parent::beforeDelete();
    }

    public function setAvaliablePatterns()
    {
        if($customParams = TemplateCustomParams::getListByTemplate($this->template_id)) {
            foreach($customParams as $customParam) {
                $this->_avaliable_patterns[] = $customParam['placeholder'];
            }
        }
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    public function getTablet()
    {
        return $this->hasOne(Tablet::className(), ['id' => 'tablet_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getSignatures()
    {
        return $this->hasMany(DocumentSignature::className(), ['document_id' => 'id'])->orderBy(['position' => SORT_ASC]);
    }

    public function setContent()
    {
        $this->content = $this->template->content;
        $this->full_content = $this->content;
        return $this->content;
    }

    public function setFullContent()
    {
        if(!$this->full_content) {
            $this->full_content = \Yii::$app->controller->renderPartial('template', [
                'model' => $this,
                'template' => $this->template,
                'tablet' => $this->tablet,
            ]);
        }
        return $this->full_content;
    }

    public function clinicResponse()
    {
        $data = [
            'clinic_full_name' => 'полное наименование ĸлиниĸи',
            'clinic_short_name' => 'ĸратĸое наименование ĸлиниĸи',
            'clinic_legal_address' => 'юридичесĸий адрес ĸлиниĸи',
            'clinic_address' => 'фаĸтичесĸий адрес ĸлиниĸи',
            'director_clinic_fio' => 'фио диреĸтора ĸлиниĸи',
            'doctor_clinic_fio' => 'фио главврача ĸлиниĸи',
            'clinic_inn' => 'инн ĸлиниĸи',
            'clinic_ogrn' => 'огрн ĸлиниĸи',
            'clinic_egrul' => 'выписĸа из егрюл ĸлиниĸи',
            'clinic_email' => 'email ĸлиниĸи',
            'clinic_licence_number' => 'номер лицензии ĸлиниĸи',
            'clinic_licence_date' => 'дата выдачи лицензии ĸлиниĸи',
            'clinic_licence_organization_name' => 'наименование организации выдавшей лицензию ĸлиниĸе',
            'clinic_licence_organization_address' => 'адрес организации выдавшей лицензию ĸлиниĸе',
            'clinic_licence_organization_phone' => 'телефон организации выдавшей лицензию ĸлиниĸе',
            'clinic_kind_activities' => 'виды деятельности ĸлиниĸи',
        ];
        return json_encode($data);
    }

    public function patientResponse()
    {
        $data = [
            'patient_name' => 'фио пациента',
            'representative_name' => 'фио заĸ-го предст-ля',
            'patient_passport' => 'паспорт пациента',
            'representative_passport' => 'паспорт заĸ-го предст-ля',
            'patient_phone' => 'моб. телефон пациента',
            'representative_phone' => 'моб. телефон заĸ-го предст-ля',
            'patient_birthdate' => 'дата рождения пациента',
            'representative_birthdate' => 'дата рождения заĸ-го предст-ля',
            'patient_address' => 'адрес пациента',
            'representative_address' => 'адрес заĸ-го предст-ля',
            'patient_email' => 'email пациента',
            'patient_cert' => 'свидетельство о рождении пациента',
            'representative_email' => 'email заĸ-го предст-ля',
            'visit_date' => 'дата визита',
        ];
        return json_encode($data);
    }

    public function isParamUsed($param_name)
    {
        return ($this->template and $this->template['params'] and array_key_exists($param_name, $this->template['params']));
    }

    public function prepareData($clinicData, $patientData, $appointmentData)
    {
        $patientData['patient_number'] = $patientData['number'];
        return [
            'search' => array_merge($this->prepareValues($clinicData, true),$this->prepareValues($patientData, true), $this->prepareValues($appointmentData, true), self::staticParams()['search']),
            'replacement' => array_merge($this->prepareValues($clinicData),$this->prepareValues($patientData), $this->prepareValues($appointmentData), self::staticParams()['replacement']),
        ];
    }

    public static function staticParams()
    {
        return [
            'search' => [
                '{текущая_дата}', '{текущее_время}'
            ],
            'replacement' => [
                date('d.m.Y'), date('H:i')
            ],
        ];
    }

    public function prepareValues($items, $subject = false)
    {
        $data = [];
        if(isset($items['patient_name'])) {
            $this->patient_name = $items['patient_name'];
        }
        if(isset($items['patient_birthdate'])) {
            $this->patient_birthday = $items['patient_birthdate'];
        }
        foreach($items as $item_mis_name => $data_value) {
            $paramNameMis = Template::paramNameByMis($item_mis_name);
            if($paramNameMis) {
                $paramName = '{'.$paramNameMis.'}';
                if($this->isParamUsed($item_mis_name)) {
                    if($subject) {
                        //$data[] = self::setSubjectParamValue($paramName, $data_value);
                        $data[] = $paramName;
                    }
                    else {
                        $data[] = self::setParamValue($item_mis_name, $data_value, $paramName, $this->template);
                    }
                }
                else {
                    if($subject) {
                        $data[] = $paramName;
                    }
                    else {
                        $data[] = '';
                    }
                }
            }

        }
        return $data;
    }

    public function getServicesNoPriceFromData($items)
    {
        $str = '';
        if(isset($items['services']) and $items['services']) {
            $str .= '<ul>';
            foreach($items['services'] as $itemService) {
                $str .= '<li>' . $itemService['title'] .'</li>';
            }
            $str .= '</ul>';
        }
        return $str;
    }

    public function getServicesListFromData($items)
    {
        $str = '';
        $count = 1;
        $totalQty = 0;
        $totalPrice = 0;
        if(isset($items['services']) and $items['services']) {
            $str .= '<table class="table-services-list">';
            $str .=   '<tr>';
            $str .=     "<th></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>Услуга</font></font></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>Стоимость</font></font></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>Кол-во</font></font></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>Скидка</font></font></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>Сумма</font></font></th>";
            $str .=   '</tr>';

            foreach($items['services'] as $itemService) {

                $totalQty += $itemService['count'];
                $totalPrice += $itemService['value'];

                $str .=   '<tr>';
                $str .=     "<td><font face='Carlito, sans-serif'><font style='font-size: 11px;'>{$count}</font></font></td>";
                $str .=     "<td><font face='Carlito, sans-serif'><font style='font-size: 11px;'>{$itemService['title']}</font></font></td>";
                $str .=     "<td><font face='Carlito, sans-serif'><font style='font-size: 11px;'>".number_format($itemService['price'], 2, ',', ' ') ." руб.</font></font></td>";
                $str .=     "<td><font face='Carlito, sans-serif'><font style='font-size: 11px;'>{$itemService['count']}</font></font></td>";
                $str .=     "<td><font face='Carlito, sans-serif'><font style='font-size: 11px;'>{$itemService['discount']}%</font></font></td>";
                $str .=     "<td><font face='Carlito, sans-serif'><font style='font-size: 11px;'>".number_format($itemService['value'], 2, ',', ' ') ." руб.</font></font></td>";
                $str .=   '</tr>';
                $count++;
            }

            $str .=   '<tr>';
            $str .=     "<th style='border: none;'></th>";
            $str .=     "<th style='border: none;'></th>";
            $str .=     "<th style='border: none;'></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>{$totalQty}</font></font></th>";
            $str .=     "<th></th>";
            $str .=     "<th><font face='Carlito, sans-serif'><font style='font-size: 11px;'>".number_format($totalPrice, 2, ',', ' ') ." руб.</font></font></th>";
            $str .=   '</tr>';
            $str .= '</table>';
        }
        return $str;
    }

    public function getPriceFullFromData($items)
    {
        $servicePrice = 0;
        if(isset($items['services']) and $items['services']) {
            foreach($items['services'] as $itemService) {
                $servicePrice += $itemService['price'];
            }
        }
        return $servicePrice;
    }

    public static function setParamValue($paramNameMis, $data_value, $paramName, Template $template = null)
    {
        if($paramNameMis == 'time_start') {
            if($timestamp = strtotime($data_value)) {
                return date('d.m.Y', $timestamp);
            }
        }
        if($paramNameMis == 'current_date') {
            return date('d.m.Y');
        }
        if($paramNameMis == 'current_time') {
            return date('H:i');
        }

        /*$checkboxParams = ['{email_пациента}', '{моб._телефон_пациента}'];
        if(in_array($paramName, $checkboxParams) and !trim($data_value)) {
            return $paramName;
        }*/
        return $data_value;
    }

    /*public function getResponseAsArray($response)
    {
        if($data = json_decode($response, true)) return $data;
        return false;
    }*/

    public function getClinics()
    {
        return Yii::$app->api->getClinics();
    }

    public function getClinic($clinic_id)
    {
        if($requestedData = Yii::$app->api->getClinics()) {
            if($data = ApiHelper::getDataFromApi($requestedData)) {
                foreach($data as $clinic) {
                    if($clinic['id'] == $clinic_id) {
                        $clinic['clinic_email'] = $clinic['email'];
                        $clinic['clinic_phone'] = $clinic['phone'];
                        $clinic['clinic_site'] = $clinic['site'];
                        $clinic['clinic_bic'] = $clinic['bic'];
                        $clinic['clinic_bank'] = $clinic['bank'];
                        $clinic['clinic_cor_account'] = $clinic['cor_account'];
                        $clinic['clinic_account'] = $clinic['account'];
                        return $clinic;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Используется разово - посмотреть id визитов
     */
    /*public function getAppointments()
    {
        $params = [
            'date_created_from' => '01.09.2023 08:00',
            'date_created_to' => '05.09.2023 08:00',
        ];

        if($response = $this->api->getAppointments($params)) {
            return $this->getResponseAsArray($response);
        }
        return false;
    }*/

    /**
     * получить визит по его id
     */
    public function getAppointment()
    {
        $params = [
            'appointment_id' => $this->appointment_id,
        ];
        if($requestedData = Yii::$app->api->getAppointments($params)) {
            $result = null;
            if($data = ApiHelper::getDataFromApi($requestedData)) {
                if(isset($data[0])) {
                    $result = $data[0];
                    $result['visit_date'] = isset($result['time_start']) ? date('d.m.Y', strtotime($result['time_start']))  : '';
                    $result['time_from'] = isset($result['time_start']) ? date('H:i', strtotime($result['time_start']))  : '';
                    $result['services_no_price'] = $this->getServicesNoPriceFromData($result);
                    $result['service_list'] = $this->getServicesListFromData($result);
                    $result['price_full'] = $this->getPriceFullFromData($result);
                    $result['user_name_short'] = $result['author_name'];
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * получить пациента по его id
     */
    public function getPatient($patient_id)
    {
        $this->patient_id = $patient_id;
        $params = [
            'id' => $patient_id,
            'with_documents' => true,
        ];
        if($requestedData = Yii::$app->api->getPatient($params)) {
            $data = ApiHelper::getDataFromApi($requestedData);
            $data['patient_name'] = $data['last_name'].' '.$data['first_name'].' '.$data['third_name'];
            $data['patient_address'] = ($data['address'] and isset($data['address']['fullAddress'])) ? $data['address']['fullAddress'] : '';
            $data['patient_birthdate'] = $data['birth_date'];
            $data['patient_number'] = $data['number'];
            $data['patient_email'] = $data['email'];
            $data['patient_phone'] = $data['mobile'];
            $data['patient_passport'] = ($data['documents'] and $data['documents']['passport']) ? $data['documents']['passport'] : '';
            $data['patient_insurance'] = ($data['documents'] and $data['documents']['insurance']) ? $data['documents']['insurance'] : '';
            $data['patient_cert'] = ($data['documents'] and $data['documents']['cert']) ? $data['documents']['cert'] : '';
            $data['patient_address_legal'] = ($data['address'] and $data['address']['fullAddress']) ? $data['address']['fullAddress'] : '';
            $data['patient_fact_address'] = ($data['address'] and $data['address']['fullAddress']) ? $data['address']['fullAddress'] : '';
            $data['representative_name'] = '';
            $data['representative_address'] = '';
            $data['representative_birthdate'] = '';
            $data['representative_email'] = '';
            $data['representative_phone'] = '';
            $data['representative_passport'] = '';

            if($parent_id = $data['parent_id']) {
                sleep(1);
                $params = [
                    'id' => $parent_id,
                    'with_documents' => true,
                ];
                if($representativeData = Yii::$app->api->getPatient($params)) {
                    if($representativeData = ApiHelper::getDataFromApi($representativeData)) {
                        $data['representative_name'] = $representativeData['last_name'].' '.$representativeData['first_name'].' '.$representativeData['third_name'];
                        $data['representative_address'] = ($representativeData['address'] and isset($representativeData['address']['fullAddress'])) ? $representativeData['address']['fullAddress'] : '';
                        $data['representative_birthdate'] = $representativeData['birth_date'];
                        $data['representative_email'] = $representativeData['email'];
                        $data['representative_phone'] = $representativeData['mobile'];
                        $data['representative_passport'] = ($representativeData['documents'] and $representativeData['documents']['passport']) ? $representativeData['documents']['passport'] : '';
                    }
                }
            }
            return $data;
        }
        return false;
    }

    public function getCompany()
    {
        $this->patient_id = $patient_id;
        $params = [
            'id' => $patient_id,
            'with_documents' => true,
        ];
        if($requestedData = Yii::$app->api->getPatient($params)) {
            $data = ApiHelper::getDataFromApi($requestedData);
            $data['patient_name'] = $data['last_name'].' '.$data['first_name'].' '.$data['third_name'];
            $data['patient_address'] = ($data['address'] and isset($data['address']['fullAddress'])) ? $data['address']['fullAddress'] : '';
            $data['patient_birthdate'] = $data['birth_date'];
            $data['patient_number'] = $data['number'];
            $data['patient_email'] = $data['email'];
            $data['patient_phone'] = $data['mobile'];
            $data['patient_passport'] = ($data['documents'] and $data['documents']['passport']) ? $data['documents']['passport'] : '';
            $data['patient_insurance'] = ($data['documents'] and $data['documents']['insurance']) ? $data['documents']['insurance'] : '';
            $data['patient_address_legal'] = ($data['address'] and $data['address']['fullAddress']) ? $data['address']['fullAddress'] : '';
            $data['representative_name'] = '';
            $data['representative_address'] = '';
            $data['representative_birthdate'] = '';
            $data['representative_email'] = '';
            $data['representative_phone'] = '';
            $data['representative_passport'] = '';

            if($parent_id = $data['parent_id']) {
                $params = [
                    'id' => $parent_id,
                ];
                if($responseRequestedRepresentative = Yii::$app->api->getPatient($params)) {
                    $representativeData = ApiHelper::getDataFromApi($responseRequestedRepresentative);
                    if($representativeData and isset($representativeData['data']) and $representativeData['data']) {
                        $data['representative_name'] = $representativeData['last_name'].' '.$representativeData['first_name'].' '.$representativeData['third_name'];
                        $data['representative_address'] = ($representativeData['address'] and isset($representativeData['address']['fullAddress'])) ? $representativeData['address']['fullAddress'] : '';
                        $data['representative_birthdate'] = $representativeData['birth_date'];
                        $data['representative_email'] = $representativeData['email'];
                        $data['representative_phone'] = $representativeData['mobile'];
                        $data['representative_passport'] = ($representativeData['documents'] and $representativeData['documents']['passport']) ? $representativeData['documents']['passport'] : '';
                    }
                }
            }
            return $data;
        }
        return false;
    }

    public function addDocumentError($message = '')
    {
        $this->documentErrors[] = $message;
    }

    public function hasDocumentErrors()
    {
        return !empty($this->documentErrors);
    }

    public function getErrorsMessage()
    {
        return implode(',', $this->documentErrors);
    }

    public function checkRequiredFields()
    {
        $pattern = "/{.*?}/";
        preg_match_all($pattern, $this->full_content, $matches);
        if(!$matches) {
            return true;
        }
        if(!$template = $this->template) {
            $this->addDocumentError('Выбранного шаблона не существует');
            return false;
        }
        if(!$template['params']) {
            $this->addDocumentError('Не заполнены параметры шаблона');
            return false;
        }
        $matches = $matches[0];
        $matches = array_unique($matches);

        /*if($matches) {
            $avaliableKeys = Template::getAvaliableKeys();
            foreach($matches as $match) {
                if(in_array($match, $avaliableKeys)) {
                    $key = array_search($match, $matches);
                    unset($matches[$key]);
                }
            }
        }*/

        // все ли поля заполнены в шаблоне
        $matchesRequired = [];
        foreach($template['params'] as $paramName => $paramValues) {
            $paramName = Template::paramNameByMis($paramName);
            if($paramName) {
                $fullParamName = '{'.$paramName.'}';
                if($paramValues['required'] and !in_array($fullParamName, $matches)) {
                    $matchesRequired[] = $fullParamName;
                }
            }
        }

        /*foreach($template['params'] as $paramName => $paramValues) {
            $paramName = Template::paramNameByMis($paramName);
            if($paramName) {
                $fullParamName = '{'.$paramName.'}';
                if(in_array($fullParamName, $matches) and $paramValues['required']) {
                    $key = array_search($fullParamName, $matches);
                    unset($matches[$key]);
                }
            }
        }*/

        if($matchesRequired) {
            $matchesStr = '';
            foreach($matchesRequired as $match) {
                $matchesStr .= $match.' ';
            }
            $this->addDocumentError('В шаблоне отсутствуют обязательные поля: '.$matchesStr);
            return false;
        }
        return true;
    }

    public function savePdf($content)
    {
        return $this->generatePdf($content);
    }

    public function generatePdf($empty = false)
    {
        $pdfDir = \Yii::getAlias('@app/web').'/pdf/';
        if(!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755);
        }

        //$cssFileName = \Yii::getAlias('@app/web').'/css/pdf.css';
        $cssFileName = \Yii::getAlias('@app/web').'/css/pdf-styles.css';
        $pdf = \Yii::$app->pdf;
        $pdf->content = $this->content;
        $pdf->destination = Pdf::DEST_FILE;
        $pdf->cssFile = $cssFileName;
        $pdf->cssInline = Setting::findOne(['key' => 'document_css'])->value ?? '';

        $dopText = $empty ? '_empty' : '';
        $documentName = $this->id.'_'.date('d.m.y', $this->created_at).'_'.mt_rand(10000,100000).$dopText.'.pdf';
        $pdf->filename = 'pdf/'.$documentName;
        $this->document_name = $documentName;
        $this->save();
        return $pdf->render();
    }

    public function uploadFile()
    {
        //return true;
        if($this->document_name) {
            if($content = file_get_contents(\Yii::getAlias('@app/web/').'pdf/'.$this->document_name)) {
                if($encode_content = base64_encode($content)) {
                    $params = [
                        'content' => $encode_content,
                        'title' => $this->document_name,
                        'patient_id' => $this->patient_id,
                        'type' => 'pdf',
                        'document_type' => 'admin',
                    ];
                    $response = Yii::$app->api->uploadFile($params);
                    if(!$data = ApiHelper::getDataFromApi($response)) {
                        $this->addDocumentError('Произошла ошибка загрузки документа');
                    }
                    /*if(!$response or $response['error'] and (isset($response['data']))) {
                        $this->addDocumentError($response['data']['desc']);
                    }*/
                    return $response;
                }
            }
        }
        return false;
    }

    public function saveSignatures($signatures)
    {
        if($signatures) {
            foreach($signatures as $signatureID => $signaturePath) {
                $model = new DocumentSignature();
                $model->document_id = $this->id;
                $model->signature_id = $signatureID;
                $model->signature_path = $signaturePath;
                if(!$model->save()) {
                    return false;
                }
            }
            $this->is_signature = 1;
            return $this->save();
        }
    }

    public function upload()
    {

    }


    public function getPdfFileName()
    {
        if(!file_exists(self::FILE_PATH)) {
            mkdir(self::FILE_PATH, 0777);
        }
        return self::FILE_PATH;
    }


    public function prepareSignatures($signatures)
    {
        // обрабатываем массив с подписями - в ключах ставим обратные скобки
        $signaturesPatterns = [];
        foreach($signatures as $signatureID => $signaturePath) {
            $signaturesPatterns['{'.$signatureID.'}'] = $signaturePath;
        }
        return $signaturesPatterns;
    }


    /**
     * вставляет подписи в контент
     */
    public function contentWithSignatures($signatures)
    {
        if(!$signatures) return false;

        $signaturesPatterns = $this->prepareSignatures($signatures);
        // фраза для замены из шаблона
        $patternStr = '{место_для_подписи}';

        // получаем контент с {signature_ID} вместо места для подписи
        $contentArr = explode($patternStr, $this->content);
        $fullContent = '';
        if($contentArr) {
            $i = 0;
            foreach($contentArr as $contentPart) {
                if($i == 0) {
                    $fullContent .= $contentPart;
                }
                else {
                    $pattern = '{signature_'.$i.'}';
                    $fullContent .= $pattern.$contentPart;
                }
                $i++;
            }
        }
        else {
            $fullContent = $this->content;
        }
        foreach($signaturesPatterns as $signaturePattern => $signaturePath) {
            $fullContent = str_replace($signaturePattern, $signaturePath, $fullContent);
        }
        $this->content = $fullContent;
        return $this->content;
    }

    protected function getCustomByIndex($customValues, $index)
    {
        $i = 1;
        foreach($customValues as $pattern => $value) {
            if($i == $index) {
                return "{" . $pattern . "}";
            }
            $i++;
        }
        return null;
    }

    public function setContentWithCustom($data)
    {
        if(!isset($data['custom']) || !$data['custom']) return false;
        $data = $this->prepareCustomFields($data['custom']);

        if($data) {
            foreach($data as $placeholder => $placeholderValues) {
                // фраза для замены из шаблона
                $patternStr = "{" . $placeholder . "}";

                // получаем контент с {custom_ID} вместо кастомного поля
                $contentArr = explode($patternStr, $this->content);
                $fullContent = '';
                if($contentArr) {
                    $i = 0;
                    foreach($contentArr as $contentPart) {
                        if($i == 0) {
                            $fullContent .= $contentPart;
                        }
                        else {
                            $pattern = $this->getCustomByIndex($placeholderValues, $i);
                            $fullContent .= $pattern.$contentPart;
                        }
                        $i++;
                    }
                }
                else {
                    $fullContent = $this->content;
                }
                foreach($placeholderValues as $pattern => $value) {
                    $fullContent = str_replace("{" . $pattern . "}", $value, $fullContent);
                }
                $this->content = $fullContent;
            }
        }
        return $this->content;
    }

    public function prepareCustomFields($customFields)
    {
        $data = [];
        foreach($customFields as $fieldId => $fieldValue) {
            $data[$fieldValue['placeholder']][$fieldValue['id']] = $fieldValue['data'];
        }
        return $data;
    }


    public function contentWithPatterns($data)
    {
        if(array_key_exists('email', $data)) {
            $patterns = [
                '{email_пациента}' => ($data['email'] ? $data['email'] : '-'),
                '{моб._телефон_пациента}' => $data['phone'] ?? '-',
            ];
            foreach($patterns as $patternName => $patternValue) {
                $this->content = str_replace($patternName, $patternValue, $this->content);
                $this->full_content = str_replace($patternName, $patternValue, $this->full_content);
            }

            return $this->update();
        }
        return true;
    }

    public function contentResponse()
    {
        return [
            'document_id' => $this->id,
            'tablet_id' => $this->tablet_id,
            'patient_name' => $this->patient_name,
            'patient_birthday' => $this->patient_birthday,
            'patient_id' => $this->patient_id,
            //'patient_email' => $this->patient_email,
            'content' => $this->content,
            'custom_params' => $this->getCustomParams(),
        ];
    }

    public function setResultContent($totalData)
    {
        $full_content = str_replace($totalData['search'], $totalData['replacement'], $this->full_content);
        $content = str_replace($totalData['search'], $totalData['replacement'], $this->content);

        $pattern = "/{.*?}/";
        preg_match_all($pattern, $full_content, $matches);

        if($matches and isset($matches[0]) and ($matches = $matches[0])) {
            foreach($matches as $match) {
                $unReplacedStr = str_replace(['{', '}'], ['', ''], $match);
                if(!in_array($unReplacedStr, $this->_avaliable_patterns)) {
                    $full_content = str_replace($match, '', $full_content);
                    $content = str_replace($match, '', $content);
                }
            }
        }
        $this->full_content = $full_content;
        $this->content = $content;

        //$model->full_content = preg_replace($pattern, '', $model->full_content);
        //$model->content = preg_replace($pattern, '', $model->content);
    }

    public function hasCustomParams()
    {
        if(!$this->template) return false;

        $customParams = TemplateCustomParams::getListByTemplate($this->template_id);

        foreach($customParams as $customParam) {
            $placeholder = $customParam['placeholder'];
            if(preg_match("/{$placeholder}/", $this->content)) return true;
        }

        return false;
    }

    public function getAppointmentErrorMessage()
    {
        return \Yii::$app->controller->renderPartial('//document/_patient_alert', [
            'class' => 'danger',
            'text' => 'Визит не найден',
        ]);
    }

    public function getAppointmentSuccessMessage($message)
    {
        return \Yii::$app->controller->renderPartial('//document/_patient_alert', [
            'class' => 'success',
            'text' => $message,
        ]);
    }

    public function checkAppointment()
    {
        $pattern = "/\D/";
        return preg_match($pattern, $this->appointment_id);
    }

    public function getCancelSvg()
    {
        $svgCancel = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" fill="currentColor" class="bi bi-ban-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM2.71 12.584c.218.252.454.488.706.707l9.875-9.875a7.034 7.034 0 0 0-.707-.707l-9.875 9.875Z"/>
</svg>';
        $svgUnCancel = '<svg xmlns="http://www.w3.org/2000/svg" width="16" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
</svg>';
        return !$this->canceled ? $svgUnCancel : $svgCancel;
    }

    public function getCancelConfirmText()
    {
        return $this->canceled ? 'Вы уверены, что хотите вернуть документ?' : 'Вы уверены, что хотите отменить документ?';
    }

    public function cancelDocuments()
    {
        if(!Setting::findOne(['key' => 'cancel_unsigned'])->value) return false;
        Document::updateAll(['canceled' => 1], ['and', ['!=', 'id', $this->id], ['=', 'tablet_id',  $this->tablet_id]]);
    }

    public function sendDocEmail($data)
    {
        return true;
        if(isset($data['email']) and isset($data['send_email']) and $data['send_email']) {
            $settings = Settings::getInstance();
            if($settings->doc_email_subject and $settings->doc_email_text) {
                return Yii::$app->mailer->compose()
                    ->setFrom([Yii::$app->params['senderEmail'] => Setting::findOne(['key' => 'app_name'])->value])
                    ->setTo($data['email'])
                    ->setSubject($settings->doc_email_subject)
                    ->setHtmlBody($settings->doc_email_text)
                    ->attach(\Yii::getAlias('@app/web/').'pdf/'.$this->document_name)
                    ->send();
            }
        }
    }

    public function hasAdminCustomParams()
    {
        if($this->customParams) {
            foreach($this->customParams as $customParam) {
                if(TemplateCustomParams::isAdminParam($customParam['type'])) return true;
            }
        }
        return false;
    }

    public function getCustomParams()
    {
        $tplCustomParams = TemplateCustomParams::getListByTemplate($this->template_id);
        if ($this->custom_params) {
            $indexed1 = ArrayHelper::index($tplCustomParams, 'id');
            $indexed2 = ArrayHelper::index($this->custom_params, 'id');
            return array_values(array_replace_recursive($indexed1, $indexed2));
        }
        return $tplCustomParams;
    }

    public function getCustomPatterns()
    {
        return array_map(function ($param) {
            return $param['placeholder'];
        }, $this->customParams);
    }

    public function applyCustomParams()
    {
        foreach ($this->customParams as $param) {
            if(!TemplateCustomParams::isAdminParam($param['type'])) continue;
            $this->content = str_replace('{' . $param['placeholder'] . '}', $param['value'], $this->content);
            $this->full_content = str_replace(
                '{' . $param['placeholder'] . '}',
                $param['value'],
                $this->full_content
            );
        }
    }
}
