<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%statuses}}`.
 */
class m220831_071442_create_statuses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%statuses}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(128)->notNull(),
            'code' => $this->string(128)->notNull()
        ]);

        $this->dropColumn('tasks', 'status');
        $this->addColumn('tasks', 'status_id', $this->integer()->notNull());
        $this->addForeignKey('status', 'tasks', 'status_id', 'statuses', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('status', 'tasks');
        $this->dropColumn('tasks', 'status_id');
        $this->addColumn('tasks', 'status', $this->string(128)->notNull());
        $this->dropTable('{{%statuses}}');
    }
}
