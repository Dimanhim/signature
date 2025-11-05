<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
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

    public function getClinicName()
    {
        $clinics = Api::getClinicsList();
        if ($this->clinic_id and isset($clinics[$this->clinic_id])) {
            return $clinics[$this->clinic_id];
        }
        return false;
    }

    public static function getListForCurrentUser()
    {
        $userId = Yii::$app->user->id;

        $userClinicId = User::findOne(['id' => $userId])->clinic_id;

        if (!$userClinicId) {
            return self::getList();
        }

        $tablets = self::find()
            ->where(['clinic_id' => $userClinicId])
            ->all();
        return ArrayHelper::map($tablets, 'id', 'name');
    }

    public static function getDefaultForCurrentUser()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne(['id' => $userId]);

        return $user->default_tablet_id;
    }

    public function getLink()
    {
        return Url::to(['tablet/' . $this->id]);
        return Setting::findOne(['key' => 'tablet_url'])->value.$this->id;
    }


}
