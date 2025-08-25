<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m230901_145906_template_params
 */
class m230901_145906_template_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%template_params}}', [
            'id'                    => Schema::TYPE_PK,

            'template_id'           => Schema::TYPE_INTEGER,
            'param_name'            => Schema::TYPE_STRING,
            'required'              => Schema::TYPE_BOOLEAN,

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
        $this->dropTable('{{%template_params}}');
    }
}
