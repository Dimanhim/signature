<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alfa_payments".
 *
 * @property int $id
 * @property int $appointment_id
 * @property string $invoice_number
 * @property int $patient_id
 * @property string|null $payment_link
 * @property int|null $is_payed
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Payment extends \app\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alfa_payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['appointment_id', 'invoice_number'], 'required'],
            [['appointment_id', 'patient_id', 'is_payed'], 'integer'],
            [['payment_link'], 'string'],
            [['invoice_number', 'invoice_number_real'], 'string', 'max' => 255],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'appointment_id' => 'Appointment ID',
            'invoice_number' => 'Invoice Number',
            'patient_id' => 'Patient ID',
            'payment_link' => 'Payment Link',
            'is_payed' => 'Is Payed',
        ]);
    }
}
