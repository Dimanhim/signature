<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ec_industries".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_en
 * @property string|null $name_fra
 * @property int|null $position
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Template extends BaseModel
{

    public $params = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%templates}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'template';
    }

    public static function modelName()
    {
        return 'Шаблоны';
    }

    /**
     * @return int
     */
    public static function typeId()
    {
        return Gallery::TYPE_TEMPLATE;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['params'], 'safe'],
            [['content'], 'string'],
            [['clinic_ids'], 'safe'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название',
            'params' => 'Параметры',
            'clinic_ids' => 'Доступно для филиалов',
        ]);
    }

    public function beforeSave($insert)
    {
        if (is_array($this->clinic_ids)) {
            $this->clinic_ids = json_encode($this->clinic_ids);
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->setParams();
        if (!$this->content) {
            $this->content = Yii::$app->controller->renderPartial('//document/_document_content');
        }

        $this->clinic_ids = json_decode($this->clinic_ids, true) ?: [];

        return parent::afterFind();
    }

    public function getCutName()
    {
        return str_replace(' ', '_', $this->name);
    }

    public static function getListForCurrentUser()
    {
        $userId = Yii::$app->user->id;

        $userClinicId = User::findOne(['id' => $userId])->clinic_id;

        if (!$userClinicId) {
            return self::getList();
        }

        $templates = self::find()
            ->where(['regexp', 'clinic_ids', '"' . $userClinicId . '"'])
            ->all();
        return ArrayHelper::map($templates, 'id', 'name');
    }

    public static function getParamsArray()
    {
        return [
            'Клиника' => [
                'full_title' => 'полное_наименование_клиники',              // clinic
                'title' => 'краткое_наименование_клиники',                  // clinic
                'legal_address' => 'юридический_адрес_клиники',             // clinic
                'real_address' => 'фактический_адрес_клиники',              // clinic
                'director_name' => 'фио_директора_клиники',                 // clinic
                'doctor_name' => 'фио_главврача_клиники',                   // clinic
                'inn' => 'инн_клиники',                                     // clinic
                'bin' => 'огрн_клиники',                                    // clinic
                'kpp' => 'кпп_клиники',                                     // clinic
                //'clinic_egrul' => 'выписка_из_егрюл_клиники',               // в ответе нет
                'clinic_email' => 'email_клиники',                                 // clinic
                'license_number' => 'номер_лицензии_клиники',               // clinic
                'license_date' => 'дата_выдачи_лицензии_клиники',           // clinic
                //'clinic_licence_organization_name' => 'наименование_организации,_выдавшей_лицензию_клинике',    // в ответе нет
                //'clinic_licence_organization_address' => 'адрес_организации,_выдавшей_лицензию_клинике',        // в ответе нет
                //'clinic_licence_organization_phone' => 'телефон_организации,_выдавшей_лицензию_клинике',        // в ответе нет
                //'clinic_kind_activities' => 'виды_деятельности_клиники',                                        // в ответе нет

                //'' => 'наименование_организации,_выдавшей_лицензию_клинике',
                //'' => 'адрес_организации,_выдавшей_лицензию_клинике',
                //'' => 'виды_деятельности_клиники',
                'clinic_phone' => 'телефон_клиники',
                'clinic_site' => 'сайт_клиники',
                'clinic_bic' => 'бик_клиники',
                'clinic_bank' => 'наименование_банка_клиники',
                'clinic_cor_account' => 'корреспондентский_счет_клиники',
                'clinic_account' => 'расчетный_счет_клиники',
            ],
            'Пациент' => [
                'patient_name' => 'фио_пациента',                                   // patient - обработка
                'representative_name' => 'фио_зак-го_предст-ля',                    // patient - обработка
                'patient_passport' => 'паспорт_пациента',                           // patient - обработка
                'representative_passport' => 'паспорт_зак-го_предст-ля',            // patient - обработка
                'patient_phone' => 'моб._телефон_пациента',                         // patient - обработка
                'representative_phone' => 'моб._телефон_зак-го_предст-ля',          // patient - обработка
                'patient_birthdate' => 'дата_рождения_пациента',                    // patient - обработка
                'representative_birthdate' => 'дата_рождения_зак-го_предст-ля',     // patient - обработка
                'patient_address' => 'адрес_пациента',                              // patient - обработка
                'representative_address' => 'адрес_зак-го_предст-ля',               // patient - обработка
                //'patient_email' => 'email_пациента',                                // patient - обработка
                'representative_email' => 'email_зак-го_предст-ля',                 // patient - обработка
                'patient_number' => '№_карточки_пациента',                                 // patient - обработка
                //'visit_date' => 'дата_визита',                                      // appointment
                'time_start' => 'дата_визита',                                      // appointment
                'patient_email' => 'email_пациента',                                      // patient
                'patient_cert' => 'св._о_рождении_пациента',                         // patient

                'gender' => 'пол_пациента',                                      // patient
                'company_number_type_oms' => 'номер_полиса_(омс)',               // company
                'patient_insurance' => 'снилс',                                      // patient
                'company_short_name_type_oms' => 'краткое_наименование_компании_(омс)',    // company
                'patient_address_legal' => 'адрес_регистрации_пациента',         // patient

                'patient_fact_address' => 'домашний_адрес_пациента',
            ],
            'Визиты' => [
                'room' => 'кабинет',
                'doctor' => 'фио_врача',

                'services_no_price' => 'список_услуг_без_цены',
                'price_full' => 'сумма_услуг',

                //'invoice_payment_title' => 'способ_оплаты_счета',
                'user_name_short' => 'фио_текущего_сотрудника_(сокращенное)',
                'time_from' => 'время_визита',
                'service_list' => 'список_услуг_(таблица_без_врача)',
                'service_list_day' => 'список_услуг_(таблица_без_врача)_за_день',
            ],
            'Другое' => [
                'signature' => 'место_для_подписи',
                'current_date' => 'текущая_дата',
                'current_time' => 'текущее_время'
            ]
        ];
    }

    public static function getAvaliableKeys()
    {
        return [
            '{выписка_из_егрюл_клиники}',
            '{наименование_организации,_выдавшей_лицензию_клинике}',
            '{адрес_организации,_выдавшей_лицензию_клинике}',
            '{телефон_организации,_выдавшей_лицензию_клинике}',
            '{виды_деятельности_клиники}',
            '{место_для_подписи}',
        ];
    }

    public static function paramNameByMis($mis_name)
    {
        $params = self::getParamsArray();
        foreach ($params as $groupName) {
            foreach ($groupName as $group_mis_name => $group_param_name) {
                if ($group_mis_name == $mis_name) {
                    return $group_param_name;
                }
            }
        }
        return false;
    }

    public function setParams()
    {
        $templateParams = TemplateParams::findAll(['template_id' => $this->id]);
        if ($templateParams) {
            foreach ($templateParams as $templateParam) {
                $this->params[$templateParam->param_name]['use'] = 1;
                $this->params[$templateParam->param_name]['required'] = $templateParam->required;
            }
        }
    }

    public function saveParams()
    {
        TemplateParams::deleteAll(['template_id' => $this->id]);
        if ($this->params) {
            foreach ($this->params as $paramName => $paramValues) {
                $this->addParam($paramName, $paramValues['required']);
            }
        }
        return true;
    }

    public function addParam($paramName, $paramRequired)
    {
        $model = new TemplateParams();
        $model->template_id = $this->id;
        $model->param_name = $paramName;
        $model->required = $paramRequired;
        return $model->save();
    }


}
