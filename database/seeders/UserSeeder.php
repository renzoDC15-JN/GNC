<?php

namespace Database\Seeders;

use App\Models\Projects;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $user= User::updateOrCreate(['email' => 'renzo.carianga@gmail.com'], ['name' => 'Renzo Carianga','password'=>Hash::make('weneverknow')]);
        $user1= User::updateOrCreate(['email' => 'cmbeltran@joy-nostalg.com'], ['name' => 'Celina Erica Beltran','password'=>Hash::make('weneverknow')]);
       $superAdminRole = Role::where('name', 'super_admin')->first();

        if ($superAdminRole) {
            // Assign super_admin role to user with id 1
            $user->roles()->syncWithoutDetaching([$superAdminRole->id]);
            $user1->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

    }
}
