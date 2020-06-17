<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use App\CompanyTypes;
use App\Player;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(1000,2000),
        'name' => $faker->unique()->city,
        'player_id' => factory(Player::class),
        'company_type' => CompanyTypes::all()->random()->id,
        'rank' => $faker->numberBetween(1,10),
        'hired_employees' => $faker->numberBetween(7,10),
        'max_employees' => $faker->numberBetween(10,15)
    ];
});
