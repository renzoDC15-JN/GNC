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
            $table->string('company_name')->nullable()->unique();
            $table->string('project_name')->nullable()->unique();
            $table->string('project_code')->nullable()->unique();
            $table->string('property_name')->nullable()->unique();
            $table->string('phase')->nullable()->unique();
            $table->string('block')->nullable()->unique();
            $table->string('lot')->nullable()->unique();
            $table->string('lot_area')->nullable()->unique();
            $table->string('floor_area')->nullable()->unique();
            $table->string('tcp')->nullable()->unique();
            $table->string('loan_term')->nullable()->unique();
            $table->string('loan_interest_rate')->nullable()->unique();
            $table->string('tct_no')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('company_name');
            $table->dropColumn('project_name');
            $table->dropColumn('project_code');
            $table->dropColumn('property_name');
            $table->dropColumn('phase');
            $table->dropColumn('block');
            $table->dropColumn('lot');
            $table->dropColumn('lot_area');
            $table->dropColumn('floor_area');
            $table->dropColumn('tcp');
            $table->dropColumn('loan_term');
            $table->dropColumn('loan_interest_rate');
            $table->dropColumn('tct_no');
        });
    }
};
