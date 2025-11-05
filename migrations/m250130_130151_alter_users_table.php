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
}
