<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230907_060434_document_signatures
 */
class m230907_060434_document_signatures extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%document_signatures}}', [
            'id'                    => Schema::TYPE_PK,

            'document_id'           => Schema::TYPE_INTEGER . ' NOT NULL',
            'signature_id'          => Schema::TYPE_STRING . ' NOT NULL',
            'signature_path'        => Schema::TYPE_TEXT,

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
        $this->dropTable('{{%document_signatures}}');
    }
}
