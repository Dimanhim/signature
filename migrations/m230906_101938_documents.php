<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230906_101938_documents
 */
class m230906_101938_documents extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%documents}}', [
            'id'                    => Schema::TYPE_PK,

            'appointment_id'        => Schema::TYPE_INTEGER . ' NOT NULL',
            'template_id'           => Schema::TYPE_INTEGER . ' NOT NULL',
            'tablet_id'             => Schema::TYPE_INTEGER . ' NOT NULL',
            'content'               => 'longtext',
            'full_content'          => 'longtext',
            'patient_id'            => Schema::TYPE_INTEGER,
            'patient_name'          => Schema::TYPE_STRING,
            'patient_birthday'      => Schema::TYPE_STRING,
            'document_name'         => Schema::TYPE_STRING,
            'is_signature'          => Schema::TYPE_INTEGER,
            'canceled'              => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'user_id'               => Schema::TYPE_INTEGER,

            'is_active'             => Schema::TYPE_SMALLINT . ' DEFAULT 1',
            'position'              => Schema::TYPE_INTEGER,
            'created_at'            => Schema::TYPE_INTEGER,
            'updated_at'            => Schema::TYPE_INTEGER,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%documents}}');
    }
}
