<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%click_log}}`.
 */
class m260218_000002_create_click_log_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%click_log}}', [
            'id' => $this->primaryKey(),
            'short_link_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-click_log-short_link_id', '{{%click_log}}', 'short_link_id');
        $this->createIndex('idx-click_log-created_at', '{{%click_log}}', 'created_at');

        $this->addForeignKey(
            'fk-click_log-short_link_id',
            '{{%click_log}}',
            'short_link_id',
            '{{%short_link}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-click_log-short_link_id', '{{%click_log}}');
        $this->dropTable('{{%click_log}}');
    }
}
