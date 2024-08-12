<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::dropIfExists('contacts');
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('reference_code')->nullable()->unique();
                $table->string('first_name');
                $table->string('middle_name');
                $table->string('last_name');
                $table->string('name_suffix')->nullable();
                $table->string('civil_status');
                $table->string('sex');
                $table->string('nationality');
                $table->date('date_of_birth');
                $table->string('email');
                $table->string('mobile');
                $table->string('other_mobile')->nullable();
                $table->string('help_number')->nullable();
                $table->string('landline')->nullable();
                $table->string('mothers_maiden_name')->nullable();
                $table->json('spouse')->nullable();
                $table->json('addresses')->nullable();
                $table->json('employment')->nullable();
                $table->json('co_borrowers')->nullable();
                $table->json('order')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
};
