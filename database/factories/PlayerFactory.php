<?php

/**
 * @var Factory $factory
 */

use App\Faction;
use App\Player;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(
    Player::class,
    function (Faker $faker) {
        return [
        'id' => $faker->unique()->numberBetween(1000, 2000),
        'faction_id' => Faction::all()->random()->id,
        'name' => $faker->name
        ];
    }
);
