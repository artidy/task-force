<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\Tasks;
use app\models\Users;
use yii\db\Expression;

return [
    'task_id' => Tasks::find()->select('id')->orderBy(new Expression('rand()'))->scalar(),
    'user_id' => Users::find()->select('id')->where('is_performer=1')->orderBy(new Expression('rand()'))->scalar(),
    'price' => rand(1000, 100000),
    'message' => $faker->text(320),
    'created_at' => $faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s'),
];
