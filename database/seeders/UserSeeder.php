<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['name' => "jhvisser"],
            [
                'password' => Hash::make(config('custom.user_account_password')),
                'email' => "justin@jhvisser.com",
            ]
        );
    }
}
