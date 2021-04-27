<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        App\User::updateOrCreate(
            ['name' => "jhvisser"],
            [
                'password' => Hash::make(config('custom.user_account_password')),
                'email' => "justin@jhvisser.com",
            ]
        );
    }
}
