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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('id_cotizacion_general');
            $table->foreign('id_cotizacion_general')->references('id')->on('cotizaciones_generales')->onDelete('Cascade');

            $table->string('descripcion');
            $table->integer('cantidad');
            $table->decimal('costo_directo');
            $table->decimal('gg');
            $table->decimal('utilidad');
            $table->decimal('total_cotizaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
