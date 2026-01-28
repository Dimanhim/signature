<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250130_150212_alter_templates_table
 */
class m250130_150212_alter_templates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%templates}}', 'clinic_ids', Schema::TYPE_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%templates}}', 'clinic_ids');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250130_150212_alter_templates_table cannot be reverted.\n";

        return false;
    }
    */
}
