<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Plan;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'gateway_id' => $faker->word,
        'price' => $faker->randomNumber(),
    ];
});
