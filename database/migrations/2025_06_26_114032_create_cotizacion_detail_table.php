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
        Schema::create('cotizacion_detail', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')->references('id')->on('servicios')->onDelete('Cascade');
            $table->unsignedBigInteger('id_cotizacion');
            $table->foreign('id_cotizacion')->references('id')->on('cotizaciones')->onDelete('Cascade');

            $table->decimal('precio_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_detail');
    }
};
