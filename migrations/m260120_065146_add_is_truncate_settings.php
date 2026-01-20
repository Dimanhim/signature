<?php

use yii\db\Migration;

class m260120_065146_add_is_truncate_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert(
            '{{%settings}}',
            ['key', 'value', 'available_for_api'],
            [
                ['lifetime_days', '7', null],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = 'DELETE FROM ' . Yii::$app->db->tablePrefix . 'settings WHERE key = "lifetime_days"';
        Yii::$app->db->createCommand($sql)->execute();
    }
}
