<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'email' => $faker->email,
    'password' => Yii::$app->getSecurity()->generatePasswordHash(123456),
    'name' => $faker->name,
    'is_performer' => $faker->boolean,
    'avatar_path' => $faker->file('web/img', 'web/assets/img', false),
    'birthday' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
    'phone_number' => substr($faker->e164PhoneNumber, 1, 11),
    'registered_at' => $faker->dateTimeBetween('-10 years')->format('Y-m-d H:i:s'),
];
