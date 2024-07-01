<?php

namespace Database\Seeders;

use App\Models\Projects;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $user= User::updateOrCreate(['email' => 'renzo.carianga@gmail.com'], ['name' => 'Renzo Carianga','password'=>Hash::make('weneverknow')]);
    }
}
