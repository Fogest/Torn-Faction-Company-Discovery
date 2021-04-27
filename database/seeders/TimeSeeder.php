<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Time::class, 250)->create();
    }
}
