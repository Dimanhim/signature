<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230901_141546_params
 */
class m230901_141546_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%templates}}', [
            'id'                    => Schema::TYPE_PK,

            'name'                  => Schema::TYPE_STRING,
            'content'               => 'longtext',

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
        $this->dropTable('{{%templates}}');
    }
}
