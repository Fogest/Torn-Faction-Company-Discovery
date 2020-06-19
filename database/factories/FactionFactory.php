<?php

/**
 * @var Factory $factory
 */

use App\Faction;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(
    Faction::class,
    function (Faker $faker) {
        return [
        'id' => $faker->unique()->numberBetween(1000, 2000),
        'name' => $faker->unique()->city,
        'current_players' => $faker->numberBetween(10, 75),
        'max_players' => $faker->numberBetween(75, 100)
        ];
    }
);
