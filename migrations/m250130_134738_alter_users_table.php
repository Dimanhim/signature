<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m250130_134738_alter_users_table
 */
class m250130_134738_alter_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'clinic_id', Schema::TYPE_INTEGER);
        $this->addColumn('{{%user}}', 'default_tablet_id', Schema::TYPE_INTEGER);
        $this->createIndex('idx-user-default-tablet-id', '{{%user}}', 'default_tablet_id');
        $this->addForeignKey(
            'fk-user-default-tablet-id',
            '{{%user}}',
            'default_tablet_id',
            '{{%tablets}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user-default-tablet-id', '{{%user}}');
        $this->dropIndex('idx-user-default-tablet-id', '{{%user}}');
        $this->dropColumn('{{%user}}', 'clinic_id');
        $this->dropColumn('{{%user}}', 'default_tablet_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250130_134738_alter_users_table cannot be reverted.\n";

        return false;
    }
    */
}
