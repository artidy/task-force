<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%tasks}}`.
 */
class m220903_162951_add_location_columns_to_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('tasks_ibfk_4', 'tasks');
        $this->dropColumn('tasks', 'location_id');

        $this->addColumn('tasks', 'city_id', $this->integer());
        $this->addColumn('tasks', 'location', $this->string());
        $this->addColumn('tasks', 'lat', $this->float());
        $this->addColumn('tasks', 'long', $this->float());
        $this->addForeignKey('tasks_cities_ibfk_1', 'tasks', 'city_id', 'cities', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('tasks_cities_ibfk_1', 'tasks');

        $this->dropColumn('tasks', 'long');
        $this->dropColumn('tasks', 'lat');
        $this->dropColumn('tasks', 'location');
        $this->dropColumn('tasks', 'city_id');

        $this->addColumn('tasks', 'location_id', $this->integer());
        $this->addForeignKey('tasks_ibfk_4', 'tasks', 'location_id', 'cities', 'id');
    }
}
