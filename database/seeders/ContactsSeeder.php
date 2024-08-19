<?php

namespace Database\Seeders;

use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Contact::factory()->count(50)->create();
    }
}
