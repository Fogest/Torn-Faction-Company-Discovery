<?php

use Illuminate\Database\Seeder;

class FactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factions')->insertOrIgnore(
            [
            ['id' => 366, 'name' => 'Evolution'],
            ['id' => 8954, 'name' => 'Nuclear Armageddon'],
            ['id' => 8085, 'name' => 'Nuclear Blast'],
            ['id' => 21028, 'name' => 'Nuclear Clinic'],
            ['id' => 12863, 'name' => 'Nuclear Development'],
            ['id' => 13851, 'name' => 'Nuclear Engineering'],
            ['id' => 9745, 'name' => 'Emergency Room'],
            ['id' => 17133, 'name' => 'Torn Medical'],
            ]
        );
    }
}
