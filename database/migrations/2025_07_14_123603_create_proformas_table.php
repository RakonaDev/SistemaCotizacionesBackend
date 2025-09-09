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
        Schema::create('proformas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('asunto');
            $table->string('lugar_entrega');
            $table->string('forma_pago');
            $table->string('moneda');
            $table->string('fecha_inicial');
            $table->string('fecha_entrega');
            $table->string('dias');
            $table->decimal('subtotal');
            $table->string('pdf')->nullable();
            $table->decimal('descuento')->nullable();
            $table->decimal('valor_venta');
            $table->decimal('igv');
            $table->decimal('importe_total');

            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_vendedor');

            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('id_vendedor')->references('id')->on('vendedores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proformas');
    }
};
