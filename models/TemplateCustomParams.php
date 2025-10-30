<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "template_custom_params".
 *
 * @property int $id
 * @property int $template_id
 * @property string $placeholder
 * @property int $type
 * @property string $description
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class TemplateCustomParams extends BaseModel
{
    const TYPE_ADMIN_TEXT   = 1;
    const TYPE_ADMIN_SWITCH = 2;
    const TYPE_USER_TEXT    = 3;
    const TYPE_USER_SWITCH  = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%template_custom_params}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'template_custom_params';
    }

    /**
     * @return int
     */
    public static function typeId()
    {
        return Gallery::TYPE_ANY;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['template_id', 'type'], 'integer'],
            [['placeholder'], 'string', 'max' => 255],
            [['description'], 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'template_id' => 'Шаблон',
            'placeholder' => 'Плейсхолдер',
            'type' => 'Тип',
            'typeName' => 'Тип',
            'description' => 'Описание',
        ]);
    }

    /**
     * @return string
     */
    public static function modelName()
    {
        return 'Параметры шаблона';
    }

    /**
     * @return array
     */
    public static function typeLabels()
    {
        return [
            self::TYPE_ADMIN_TEXT   => 'Текст для админа',
            self::TYPE_ADMIN_SWITCH => 'Да/нет для админа',
            self::TYPE_USER_TEXT    => 'Текст для пользователя',
            self::TYPE_USER_SWITCH  => 'Да/нет для пользователя',
        ];
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::typeLabels()[$this->type];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public static function searchByTemplate($templateId)
    {
        return new ActiveDataProvider([
            'query' => self::find()
                ->where(['template_id' => $templateId]),
        ]);
    }

    public static function getListByTemplate($templateId)
    {
        $templateParams = self::find()->where(['template_id' => $templateId])->all();
        $result = [];

        foreach ($templateParams as $templateParam) {
            $result[] = [
                'id' => $templateParam->id,
                'type' => $templateParam->type,
                'placeholder' => $templateParam->placeholder,
                'description' => $templateParam->description,
            ];
        }

        return $result;
    }

    public static function isAdminParam($type)
    {
        return $type === self::TYPE_ADMIN_TEXT || $type === self::TYPE_ADMIN_SWITCH;
    }
}
