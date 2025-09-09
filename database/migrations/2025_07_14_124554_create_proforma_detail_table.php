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
        Schema::create('proforma_detail', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('UM');
            $table->integer('cantidad');
            $table->decimal('precio_unit');
            $table->decimal('descuento');
            $table->decimal('total');

            $table->unsignedBigInteger('id_proforma');
            $table->foreign('id_proforma')->references('id')->on('proformas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_detail');
    }
};
