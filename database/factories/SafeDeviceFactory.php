<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SafeDevice;
use Faker\Generator as Faker;

$factory->define(SafeDevice::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class),
        'name' => $faker->word,
        'ip' => $faker->ipv4,
        'token' => $faker->uuid,
        'added_at' => $faker->date('Y-m-d H:i:s')
    ];
});
