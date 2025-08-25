<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230901_133701_tablets
 */
class m230901_133701_tablets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tablets}}', [
            'id'                    => Schema::TYPE_PK,

            'name'                  => Schema::TYPE_STRING,
            'clinic_id'             => Schema::TYPE_INTEGER,

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
        $this->dropTable('{{%tablets}}');
    }
}
