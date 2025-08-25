<?php

use yii\db\Migration;
use yii\db\Schema;

class m250114_162432_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id'                    => Schema::TYPE_PK,
            'key'                   => Schema::TYPE_STRING,
            'value'                 => Schema::TYPE_TEXT,
            'available_for_api'     => Schema::TYPE_SMALLINT,
            'created_at'            => Schema::TYPE_INTEGER,
            'updated_at'            => Schema::TYPE_INTEGER,
        ]);

        $this->batchInsert(
            '{{%settings}}',
            ['key', 'value', 'available_for_api'],
            [
                ['app_name', 'Сервис подписи', '0'],
                ['rnova_api_url', 'https://app.rnova.org/api/public/', '0'],
                ['rnova_api_key', '', '0'],
                ['tablet_url', 'https://localhost/tablet/?tabletId=', '0'],
                ['cancel_unsigned', '0', '0'],
                ['update_on_demand', '0', '1'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
