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
        Schema::table('update_logs', function (Blueprint $table) {
            $table->uuid('loggable_id')->change();  // Change this to UUID type to handle UUIDs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('update_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('loggable_id');
        });
    }
};
