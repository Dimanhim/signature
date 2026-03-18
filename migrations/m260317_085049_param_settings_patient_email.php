<?php

use yii\db\Migration;

class m260317_085049_param_settings_patient_email extends Migration
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
                ['send_patient_email', '0', '0'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DELETE FROM `{{%settings}} WHERE `key` = "send_patient_email"`');
    }
}
