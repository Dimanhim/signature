<?php

use yii\db\Migration;

/**
 * Class m260407_060920_extend_template_payment_option
 */
class m260407_060920_extend_template_payment_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%templates}}', 'payment_option', $this->tinyInteger(1)->defaultValue(0)->after('content'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%templates}}', 'payment_option');
    }
}
