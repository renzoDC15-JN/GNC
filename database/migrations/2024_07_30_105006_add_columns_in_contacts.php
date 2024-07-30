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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('other_mobile')->nullable();
            $table->string('help_number')->nullable();
            $table->string('landline')->nullable();
            $table->string('mothers_maiden_name')->nullable();
//            $table->json('spouse')->nullable();
//            $table->json('addresses')->nullable();
//            $table->json('employment')->nullable();
//            $table->json('co_borrowers')->nullable();
//            $table->json('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('other_mobile');
            $table->dropColumn('help_number');
            $table->dropColumn('landline');
            $table->dropColumn('mothers_maiden_name');
//            $table->dropColumn('spouse')->nullable();
//            $table->dropColumn('addresses')->nullable();
//            $table->dropColumn('employment')->nullable();
//            $table->dropColumn('co_borrowers')->nullable();
//            $table->dropColumn('order')->nullable();
        });
    }
};
