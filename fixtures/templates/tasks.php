<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Categories;
use app\models\Cities;
use app\models\Users;
use yii\db\Expression;

return [
    'title' => $faker->sentence,
    'description' => $faker->text(320),
    'category_id' => Categories::find()->select('id')->orderBy(new Expression('rand()'))->scalar(),
    'client_id' => Users::find()->select('id')->where(['is_performer' => 0])->orderBy(new Expression('rand()'))->scalar(),
    'location_id' => $faker->boolean ? Cities::find()->select('id')->orderBy(new Expression('rand()'))->scalar() : null,
    'budget' => rand(1000, 100000),
    'created_at' => $faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s'),
    'status_id' => 1,
];
