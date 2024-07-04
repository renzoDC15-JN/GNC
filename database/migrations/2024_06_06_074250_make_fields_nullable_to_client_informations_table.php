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
        Schema::table('client_informations', function (Blueprint $table) {
            $table->string('project')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('property_name')->nullable()->change();
            $table->string('phase')->nullable()->change();
            $table->string('block')->nullable()->change();
            $table->string('lot')->nullable()->change();
            $table->string('buyer_name')->nullable()->change();
            $table->string('buyer_civil_status')->nullable()->change();
            $table->string('buyer_nationality')->nullable()->change();
            $table->string('buyer_address')->nullable()->change();
            $table->string('buyer_tin')->nullable()->change();
            $table->string('buyer_spouse_name')->nullable()->change();
            $table->string('mrif_fee')->nullable()->change();
            $table->string('reservation_rate')->nullable()->change();
            $table->string('created_by')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_informations', function (Blueprint $table) {
            //
        });
    }
};
