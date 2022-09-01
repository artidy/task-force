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

        $this->addColumn('tasks', 'reply_id', $this->integer());
        $this->addForeignKey('replies_ibfk_1', 'tasks', 'reply_id', 'replies', 'id');
        $this->addColumn('users', 'reply_id', $this->integer());
        $this->addForeignKey('replies_ibfk_2', 'users', 'reply_id', 'replies', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('replies_ibfk_2', 'users');
        $this->dropColumn('users', 'reply_id');
        $this->dropForeignKey('replies_ibfk_1', 'tasks');
        $this->dropColumn('tasks', 'reply_id');
        $this->dropTable('{{%replies}}');
    }
}
