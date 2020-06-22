<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::updateOrCreate(
            ['name' => "jhvisser"],
            [
                'password' => Hash::make(env('USER_ACCOUNT_PASSWORD', '123')),
                'email' => "justin@jhvisser.com",
            ]
        );
    }
}
