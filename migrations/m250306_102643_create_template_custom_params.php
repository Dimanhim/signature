<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250306_102643_create_template_custom_params
 */
class m250306_102643_create_template_custom_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%template_custom_params}}', [
            'id' => Schema::TYPE_PK,

            'template_id' => Schema::TYPE_INTEGER,
            'placeholder' => Schema::TYPE_STRING,
            'type' => Schema::TYPE_INTEGER,
            'description' => Schema::TYPE_TEXT,

            'is_active' => Schema::TYPE_SMALLINT . ' DEFAULT 1',
            'position' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%template_custom_params}}');
    }
}
