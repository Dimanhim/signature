<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ec_company_products".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $product_id
 * @property int|null $is_active
 * @property int|null $position
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class TemplateParams extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%template_params}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'template_params';
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
            [['template_id', 'required'], 'integer'],
            [['param_name'], 'string', 'max' => 255],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'template_id' => 'Шаблон',
            'param_name' => 'Параметр',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }
}
