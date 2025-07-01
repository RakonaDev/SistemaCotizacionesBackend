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
        Schema::create('cotizacion_caja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cotizacion_detail');
            $table->foreign('id_cotizacion_detail')->references('id')->on('cotizacion_detail')->onDelete('Cascade');
            $table->decimal('total');

            # Servicio
            $table->integer('cantidad')->nullable();
            $table->decimal('precio_unitario')->nullable();

            # Area
            $table->decimal('horas_habiles')->nullable();
            $table->decimal('hora_habil_costo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_caja');
    }
};
