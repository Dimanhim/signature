<?php

use yii\db\Migration;

/**
 * Class m260528_100000_payment_logs
 */
class m260528_100000_payment_logs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%payment_logs}}', [
            'id'             => $this->primaryKey(),
            'appointment_id' => $this->integer()->notNull(),
            'patient_id'     => $this->integer()->notNull(),
            'invoice_number' => $this->string(50)->null(),
            'response_data'  => $this->text()->null(),
            'created_at'     => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-payment_logs-appointment_id',
            '{{%payment_logs}}',
            'appointment_id'
        );

        $this->createIndex(
            'idx-payment_logs-created_at',
            '{{%payment_logs}}',
            'created_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment_logs}}');
    }
}

