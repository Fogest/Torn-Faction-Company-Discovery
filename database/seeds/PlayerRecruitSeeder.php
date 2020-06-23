<?php

use Illuminate\Database\Seeder;

class PlayerRecruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\PlayerRecruit::class, 30)->create();
    }
}
