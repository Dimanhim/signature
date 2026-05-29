<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $appointment_id
 * @property int $patient_id
 * @property string|null $invoice_number
 * @property string|null $response_data
 * @property string $created_at
 */
class PaymentLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%payment_logs}}';
    }

    public function rules()
    {
        return [
            [['appointment_id', 'patient_id'], 'required'],
            [['appointment_id', 'patient_id'], 'integer'],
            [['response_data'], 'string'],
            [['created_at'], 'safe'],
            [['invoice_number'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appointment_id' => 'ID Визита',
            'patient_id' => 'ID Пациента',
            'invoice_number' => 'Исходный счет',
            'response_data' => 'Слепок данных',
            'created_at' => 'Время тика',
        ];
    }
}
