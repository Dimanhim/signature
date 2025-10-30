<?php

use yii\db\Migration;
use yii\db\Schema;

class m250311_105400_extend_documents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%documents}}', 'custom_params', Schema::TYPE_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'custom_params');
    }
}
