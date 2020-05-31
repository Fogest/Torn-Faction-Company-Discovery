<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use App\Player;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(1000,2000),
        'player_id' => factory(Player::class)
    ];
});
