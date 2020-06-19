<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //        $this->call(UserSeeder::class);
        $this->call(PlayerSeeder::class);

        $this->call(FactionSeeder::class);
        $this->call(CompanyTypesSeeder::class);

        if (env('APP_DEBUG', false)) {
            $this->call(CompanySeeder::class);
        }
    }
}
