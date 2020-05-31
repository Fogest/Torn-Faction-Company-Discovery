<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Faction;
use App\Player;
use Faker\Generator as Faker;

$factory->define(Player::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(1000,2000),
        'faction_id' => factory(Faction::class)
    ];
});
