<?php

namespace app\components;

use yii\base\Component;

class InfoLog extends Component
{
    public static function add($name = '', $value, $fileName = 'info-log.txt')
    {
        file_put_contents($fileName, date('d.m.Y H:i:s').' '.$name.' - '.print_r($value, true)."\n", FILE_APPEND);
    }

    /**
     * Динамический метод компонента для записи логов платежей в базу
     */
    public function addPaymentLog($appointmentId, $patientId, $invoiceNumber, $allInvoices, $foundStatus)
    {
        try {
            \Yii::$app->db->createCommand()->insert('{{%payment_logs}}', [
                'appointment_id' => (int)$appointmentId,
                'patient_id'     => (int)$patientId,
                'invoice_number' => $invoiceNumber,
                'response_data'  => json_encode([
                    'allInvoices'  => $allInvoices,
                    'found_status' => (int)$foundStatus
                ], JSON_UNESCAPED_UNICODE),
            ])->execute();

            if (rand(1, 20) === 1) {
                \Yii::$app->db->createCommand(
                    "DELETE FROM {{%payment_log}} WHERE `created_at` < NOW() - INTERVAL 3 DAY"
                )->execute();
            }
        } catch (\Exception $e) {
            self::add('CRITICAL LOG ERROR', $e->getMessage(), 'emergency-log.txt');
        }
    }
}
