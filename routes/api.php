<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\CotizacionGeneralController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendedoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/login", [UsuarioController::class, 'login']);

Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [UsuarioController::class, 'logout']);
    Route::get('/me', [UsuarioController::class,'me']);
    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
    Route::get('/dashboard/cotizaciones', [DashboardController::class,'traerCotizaciones']);

    Route::apiResource('clientes', ClientesController::class);
    Route::get('/exportar-clientes', [ClientesController::class, 'exportarAExcel']);

    Route::get('/buscarCliente', [ClientesController::class,'buscar']);
    Route::get('/buscarVendedor', [VendedoresController::class,'buscar']);

    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('cotizaciones', CotizacionController::class);
    Route::apiResource('vendedores', VendedoresController::class);
    Route::apiResource('proformas', ProformaController::class);

    Route::get('/proformas/{id}/pdf', [ProformaController::class, 'descargarPdf']);
});

