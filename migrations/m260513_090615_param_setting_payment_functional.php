<?php

use yii\db\Migration;

class m260513_090615_param_setting_payment_functional extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $exists = (new \yii\db\Query())
            ->from('{{%settings}}')
            ->where(['key' => 'payment_functional'])
            ->exists();

        if (!$exists) {
            $tableSchema = Yii::$app->db->getTableSchema('{{%settings}}');

            if (isset($tableSchema->columns['available_for_api'])) {
                $this->insert('{{%settings}}', [
                    'key' => 'payment_functional',
                    'value' => '1',
                    'available_for_api' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
            } else {
                $this->insert('{{%settings}}', [
                    'key' => 'payment_functional',
                    'value' => '1',
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%settings}}', ['key' => 'payment_functional']);
    }
}
