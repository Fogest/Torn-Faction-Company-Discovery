<?php

use Illuminate\Database\Seeder;

class CompanyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('company_types')->insertOrIgnore([
            ['id' => 19, 'name' => 'Firework Stand'],
            ['id' => 3, 'name' => 'Flower Shop'],
            ['id' => 8, 'name' => 'Candle Shop'],
            ['id' => 20, 'name' => 'Property Broker'],
            ['id' => 1, 'name' => 'Hair Salon'],
            ['id' => 5, 'name' => 'Clothing Store'],
            ['id' => 27, 'name' => 'Restaurant'],
            ['id' => 14, 'name' => 'Sweet Shop'],
            ['id' => 25, 'name' => 'Pub'],
            ['id' => 7, 'name' => 'Game Shop'],
            ['id' => 23, 'name' => 'Music Store'],
            ['id' => 10, 'name' => 'Adult Novelties'],
            ['id' => 32, 'name' => 'Lingerie Store'],
            ['id' => 12, 'name' => 'Grocery Store'],
            ['id' => 9, 'name' => 'Toy Shop'],
            ['id' => 21, 'name' => 'Furniture Store'],
            ['id' => 6, 'name' => 'Gun Shop'],
            ['id' => 30, 'name' => 'Mechanic Shop'],
            ['id' => 11, 'name' => 'Cyber Cafe'],
            ['id' => 33, 'name' => 'Meat Warehouse'],
            ['id' => 2, 'name' => 'Law Firm'],
            ['id' => 26, 'name' => 'Gents Strip Club'],
            ['id' => 36, 'name' => 'Ladies Strip Club'],
            ['id' => 34, 'name' => 'Farm'],
            ['id' => 4, 'name' => 'Car Dealership'],
            ['id' => 35, 'name' => 'Software Corporation'],
            ['id' => 24, 'name' => 'Nightclub'],
            ['id' => 39, 'name' => 'Detective Agency'],
            ['id' => 29, 'name' => 'Fitness Center'],
            ['id' => 22, 'name' => 'Gas Station'],
            ['id' => 13, 'name' => 'Theater'],
            ['id' => 31, 'name' => 'Amusement Park'],
            ['id' => 18, 'name' => 'Zoo'],
            ['id' => 15, 'name' => 'Cruise Line'],
            ['id' => 37, 'name' => 'Private Security Firm'],
            ['id' => 40, 'name' => 'Logistics Management'],
            ['id' => 38, 'name' => 'Mining Corporation'],
            ['id' => 16, 'name' => 'Television Network'],
            ['id' => 28, 'name' => 'Oil Rig'],
            ['id' => 0, 'name' => 'Unknown']
        ]);
    }
}
