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
        $user = new App\User();
        $user->password = Hash::make(env('USER_ACCOUNT_PASSWORD', '123'));
        $user->name = "jhvisser";
        $user->email = "justin@jhvisser.com";
        $user->save();
    }
}
