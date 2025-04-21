<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        VehicleType::insert([
            ['name' => 'Economy', 'base_fare' => 5, 'cost_per_km' => 1],
            ['name' => 'Standard', 'base_fare' => 8, 'cost_per_km' => 1.5],
            ['name' => 'Luxury', 'base_fare' => 12, 'cost_per_km' => 2],
        ]);
    }
}
