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
        Schema::create('client_informations', function (Blueprint $table) {
            $table->id();
            $table->string('project');
            $table->string('location');
            $table->string('property_name');
            $table->string('phase');
            $table->string('block');
            $table->string('lot');
            $table->string('buyer_name');
            $table->string('buyer_civil_status');
            $table->string('buyer_nationality');
            $table->string('buyer_address');
            $table->string('buyer_tin');
            $table->string('buyer_spouse_name');
            $table->string('mrif_fee');
            $table->string('reservation_rate');
            $table->string('created_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_informations');
    }
};
