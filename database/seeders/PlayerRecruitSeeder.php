<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlayerRecruitSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        factory(App\PlayerRecruit::class, 30)->create();
    }
}
