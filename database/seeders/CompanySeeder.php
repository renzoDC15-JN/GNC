<?php

namespace Database\Seeders;

use App\Models\Companies;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        Companies::updateOrCreate(['code' => 'RDG'], ['description' => 'Residential Development Group']);
        Companies::updateOrCreate(['code' => 'EHG'], ['description' => 'Economic Housing Group']);
        Companies::updateOrCreate(['code' => 'HDG'], ['description' => 'Housing Development Group']);
    }
}
