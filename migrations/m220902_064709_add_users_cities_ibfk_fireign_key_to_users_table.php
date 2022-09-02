<?php

use yii\db\Migration;

/**
 * Class m220902_064709_add_users_cities_ibfk_fireign_key_to_users_table
 */
class m220902_064709_add_users_cities_ibfk_fireign_key_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('users_cities_ibfk', 'users', 'city_id', 'cities', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('users_cities_ibfk', 'users');
    }
}
