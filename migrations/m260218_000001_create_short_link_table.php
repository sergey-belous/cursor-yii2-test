<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%short_link}}`.
 */
class m260218_000001_create_short_link_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%short_link}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->string(2048)->notNull(),
            'short_code' => $this->string(16)->notNull(),
            'visits_count' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-short_link-short_code', '{{%short_link}}', 'short_code', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%short_link}}');
    }
}
