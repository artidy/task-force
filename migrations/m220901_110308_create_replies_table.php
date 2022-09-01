<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%replies}}`.
 */
class m220901_110308_create_replies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%replies}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
            'message' => $this->string(320)->notNull(),
            'is_accept' => $this->boolean()->defaultValue(false),
            'is_denied' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('replies_ibfk_1', 'replies', 'task_id', 'tasks', 'id');
        $this->addForeignKey('replies_ibfk_2', 'replies', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('replies_ibfk_2', 'replies');
        $this->dropForeignKey('replies_ibfk_1', 'replies');
        $this->dropTable('{{%replies}}');
    }
}
