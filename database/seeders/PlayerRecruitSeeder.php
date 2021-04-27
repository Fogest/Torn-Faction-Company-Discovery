<?php

namespace Database\Seeders;

use App\PlayerRecruit;
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
        factory(PlayerRecruit::class, 30)->create();
    }
}
