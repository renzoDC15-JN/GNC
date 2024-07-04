<?php

namespace Database\Seeders;

use App\Models\Projects;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Projects::updateOrCreate(['code' => '1'], ['description' => 'PASINAYA']);
        Projects::updateOrCreate(['code' => '2'], ['description' => 'PASINARAW']);
    }
}
