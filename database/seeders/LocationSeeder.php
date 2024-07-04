<?php

namespace Database\Seeders;

use App\Models\CivilStatus;
use App\Models\Locations;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Locations::updateOrCreate(['code' => '1'], ['description' => 'LAGUNA']);
        Locations::updateOrCreate(['code' => '2'], ['description' => 'NAIC']);
    }
}
