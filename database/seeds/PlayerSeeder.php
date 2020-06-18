<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::firstOrCreate([
            'password' => Hash::make(env('USER_ACCOUNT_PASSWORD', '123')),
            'name' => "jhvisser",
            'email' => "justin@jhvisser.com",
        ]);
    }
}
