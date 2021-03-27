<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Player;
use App\Time;
use Faker\Generator as Faker;

$factory->define(Time::class, function (Faker $faker) {
    return [
        'player_id' => factory(Player::class),
        'event_id' => $faker->unique()->firstName,
        'event_name' => $faker->unique()->firstName,
        'recurring' => $faker->boolean,
        'multiple_per_day' => $faker->boolean,
        'day_of_week' => null,
        'event_date_time' => $faker->unixTime,
    ];
});
