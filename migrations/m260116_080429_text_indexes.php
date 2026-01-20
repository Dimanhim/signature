<?php

use yii\db\Migration;
use Yii;

class m260116_080429_text_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE ' . Yii::$app->db->tablePrefix . 'documents ADD FULLTEXT INDEX idx_documents_content (content);');
        $this->execute('ALTER TABLE ' . Yii::$app->db->tablePrefix . 'documents ADD FULLTEXT idx_full_content (full_content);');
        $this->execute('ALTER TABLE ' . Yii::$app->db->tablePrefix . 'document_signatures ADD FULLTEXT idx_signature_path (signature_path);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260116_080429_text_indexes cannot be reverted.\n";

        return false;
    }
}
