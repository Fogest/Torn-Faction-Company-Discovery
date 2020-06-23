<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Player;
use App\PlayerRecruit;
use Faker\Generator as Faker;

$factory->define(PlayerRecruit::class, function (Faker $faker) {
    return [
        'player_id' => $faker->numberBetween(100, 1000),
        'faction_id' => $faker->numberBetween(0, 100),
        'recruited_by_id' => Player::all()->random()->id,
        'player_name' => $faker->name,
        'faction_name' => $faker->city,
        'recruited_by' => $faker->name,
        'is_required_stats' => $faker->boolean(70),
        'is_accepted' => $faker->boolean(15)
    ];
});
