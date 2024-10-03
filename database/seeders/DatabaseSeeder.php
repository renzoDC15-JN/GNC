<?php

namespace Database\Seeders;

use App\Models\Companies;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $this->call([
            UserSeeder::class,
            LocationSeeder::class,
            ProjectSeeder::class,
            CompanySeeder::class,
            PhilippineStandardGeographicalCodeSeeder::class,
            EmploymentStatusSeeder::class,
            EmploymentTypeSeeder::class,
            HomeOwnershipSeeder::class,
            NationalitySeeder::class,
            PostalCodeSeeder::class,
            TenureSeeder::class,
        ]);
    }
}
