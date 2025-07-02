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
        Schema::create('cotizaciones_generales', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_inicial');
            $table->dateTime('fecha_final');
            $table->integer('dias_entrega');
            $table->string('descripcion');
            $table->decimal('monto_total');
            

            $table->unsignedBigInteger('id_cliente');
            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('Cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones_generales');
    }
};
