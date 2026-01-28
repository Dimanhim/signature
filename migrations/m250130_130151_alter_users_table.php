<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250130_130151_alter_users_table
 */
class m250130_130151_alter_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('email', '{{%user}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('email', '{{%user}}', 'email', $unique = true);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250130_130151_alter_users_table cannot be reverted.\n";

        return false;
    }
    */
}
