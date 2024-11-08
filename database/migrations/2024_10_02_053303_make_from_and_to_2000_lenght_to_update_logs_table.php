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
            $table->string('from', 2000)->nullable()->change();
            $table->string('to', 2000)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('update_logs', function (Blueprint $table) {
            $table->string('from', 1000)->nullable()->change();
            $table->string('to', 1000)->nullable()->change();
        });
    }
};
