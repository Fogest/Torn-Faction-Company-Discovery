<?php

namespace Database\Seeders;

use App\Time;
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
        factory(Time::class, 250)->create();
    }
}
