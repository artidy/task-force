<?php

use yii\db\Migration;

/**
 * Class m220902_113546_change_files_table
 */
class m220902_113546_change_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('files_ibfk_1', 'files');
        $this->dropColumn('files', 'task_id');
        $this->dropColumn('files', 'file_path');
        $this->addColumn('files', 'name', $this->string(128));
        $this->addColumn('files', 'path', $this->string(320));
        $this->addColumn('files', 'user_id', $this->integer());
        $this->addColumn('files', 'created_at', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('files', 'uid', $this->string());
        $this->addColumn('files', 'size', $this->integer());

        $this->addForeignKey('files_users_ibfk_1', 'files', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220902_113546_change_files_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220902_113546_change_files_table cannot be reverted.\n";

        return false;
    }
    */
}
