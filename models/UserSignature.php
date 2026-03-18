<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "demo_user_signatures".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $signature_data
 * @property int|null $is_active
 * @property int|null $position
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserSignature extends \app\models\BaseModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_signatures}}';
    }

    /**
     * @return string
     */
    public static function typeName()
    {
        return 'user_signature';
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
            [['user_id', 'signature_data'], 'required'],
            [['user_id'], 'integer'],
            [['signature_data'], 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'user_id' => 'User ID',
            'signature_data' => 'Signature Data',
        ]);
    }
}
