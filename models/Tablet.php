<?php

namespace app\models;

use Yii;

use app\models\Setting;
use yii\helpers\Url;

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
class Tablet extends BaseModel
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tablets}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'tablet';
    }

    public static function modelName()
    {
        return 'Планшеты';
    }

    /**
     * @return int
     */
    public static function typeId()
    {
        return Gallery::TYPE_TABLET;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['clinic_id'], 'integer'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название',
            'clinic_id' => 'Филиал',
        ]);
    }

    public static function getClinicsList()
    {
        $data = [];
        $api = new Api();
        $clinicsJson = $api->getClinics();
        //\Yii::$app->infoLog->add('$clinicsJson', $clinicsJson);
        if($clinicsJson and ($clinics = json_decode($clinicsJson, true)) and isset($clinics['data']) and $clinics['data']) {
            foreach($clinics['data'] as $clinic) {
                if(isset($clinic['id']) and isset($clinic['title'])) {
                    $data[$clinic['id']] = $clinic['title'];
                }
            }
        }
        return $data;
        return [
            1 => 'Филиал 1',
            2 => 'Филиал 2',
            3 => 'Филиал 3',
            4 => 'Филиал 4',
        ];
    }

    public function getClinicName()
    {
        $clinics = self::getClinicsList();
        if($this->clinic_id and isset($clinics[$this->clinic_id])) {
            return $clinics[$this->clinic_id];
        }
        return false;
    }

    public function getLink()
    {
        return Url::to(['tablet/' . $this->id]);
        return Setting::findOne(['key' => 'tablet_url'])->value.$this->id;
    }




}
