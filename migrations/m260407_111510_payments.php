<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m260407_111510_payments
 */
class m260407_111510_payments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payments}}', [
            'id'             => Schema::TYPE_PK,

            'appointment_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'invoice_number' => Schema::TYPE_STRING . ' NOT NULL',
            'invoice_number_real' => Schema::TYPE_STRING . ' AFTER invoice_number',
            'patient_id'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'payment_link'   => Schema::TYPE_TEXT,

            'is_payed'       => Schema::TYPE_SMALLINT . ' DEFAULT 0',

            'is_active'      => Schema::TYPE_SMALLINT . ' DEFAULT 1',
            'position'       => Schema::TYPE_INTEGER,
            'created_at'     => Schema::TYPE_INTEGER,
            'updated_at'     => Schema::TYPE_INTEGER,
        ]);

        $this->createIndex('idx-payments-invoice_number', '{{%payments}}', 'invoice_number');
        $this->createIndex('idx-payments-appointment_id', '{{%payments}}', 'appointment_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payments}}');
    }
}
