<?php

use yii\db\Migration;
use yii\db\Schema;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id'                    => Schema::TYPE_PK,

            'username'              => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'auth_key'              => Schema::TYPE_STRING . ' NOT NULL',
            'password'              => Schema::TYPE_STRING . ' NOT NULL',
            'password_hash'         => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token'  => Schema::TYPE_STRING . ' UNIQUE',
            'email'                 => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'status'                => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',

            'is_active'             => Schema::TYPE_SMALLINT . ' DEFAULT 1',
            'position'              => Schema::TYPE_INTEGER,
            'created_at'            => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'            => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->insert('{{%user}}', [
            'username' => 'admin',
            'auth_key' => '',
            'password' => '123456',
            'password_hash' => '$2y$13$1W.D51rRnv9Hpbo/SxSZmeNMsZppWpnYMQeAJ9C/BzDxHYE6qMN8C',
            'password_reset_token' => null,
            'email' => 'dimanhim@list.ru',
            'status' => '10',
            'is_active' => 1,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $this->batchInsert('{{%auth_item}}',
        ['name', 'type', 'description'],
        [
            ['admin', '1', 'Администратор'],
            ['manager', '1', 'Менеджер'],
        ]);
        
        $this->insert('{{%auth_assignment}}', [
            'item_name' => 'admin',
            'user_id' => 1,
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
