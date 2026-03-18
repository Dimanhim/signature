<?php

use yii\db\Migration;
use yii\db\Schema;

class m260317_095649_user_signatures extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_signatures}}', [
            'id'                    => Schema::TYPE_PK,

            'user_id'               => Schema::TYPE_INTEGER,
            'signature_data'        => 'MEDIUMTEXT NOT NULL',

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
        $this->dropTable('{{%user_signatures}}');
    }
}
