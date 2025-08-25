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
class DocumentSignature extends BaseModel
{
    const BUTTON = '<button class="btn btn--white" type="button" data-modal-open="modal-sign">Подписать</button>';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%document_signatures}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'document_signatures';
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
            [['document_id'], 'required'],
            [['document_id'], 'integer'],
            [['signature_id'], 'string', 'max' => 255],
            [['signature_path'], 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'document_id' => 'Документ',
            'signature_id' => 'ID подписи',
            'signature_path' => 'Изображение подписи',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }
}
