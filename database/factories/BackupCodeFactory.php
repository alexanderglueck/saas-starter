<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BackupCode;
use Faker\Generator as Faker;

$factory->define(BackupCode::class, function (Faker $faker) {
    return [
        'code' => $faker->word,
        'user_id' => factory(\App\User::class),
        'used_at' => $faker->dateTime(),
    ];
});
