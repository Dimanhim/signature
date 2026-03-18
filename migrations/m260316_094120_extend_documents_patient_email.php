<?php

use yii\db\Migration;
use yii\db\Schema;

class m260316_094120_extend_documents_patient_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%documents}}',
            'patient_email',
            Schema::TYPE_STRING . ' AFTER [[patient_name]]'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%documents}}', 'patient_email');
    }
}
