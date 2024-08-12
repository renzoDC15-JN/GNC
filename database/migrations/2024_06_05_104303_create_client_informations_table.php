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
            $table->string('project')->nullable();
            $table->string('location')->nullable();
            $table->string('property_name')->nullable();
            $table->string('phase')->nullable();
            $table->string('block')->nullable();
            $table->string('lot')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_civil_status')->nullable();
            $table->string('buyer_nationality')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('buyer_tin')->nullable();
            $table->string('buyer_spouse_name')->nullable();
            $table->string('mrif_fee')->nullable();
            $table->string('reservation_rate')->nullable();
            $table->string('created_by')->nullable();
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
