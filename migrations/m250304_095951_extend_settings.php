<?php

use yii\db\Migration;
use yii\db\Schema;
use app\components\Helpers;

/**
 * Class m250304_095951_extend_settings
 */
class m250304_095951_extend_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => Schema::TYPE_PK,
            'key' => Schema::TYPE_STRING,
            'value' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
        ]);

        $this->batchInsert(
            '{{%settings}}',
            ['key', 'value'],
            [
                ['tablet_api_key', Helpers::getRandomString(20)],
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
