<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CompanyTypes;
use Faker\Generator as Faker;

$factory->define(CompanyTypes::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(0,100),
        'name' => $faker->unique()->name
    ];
});
