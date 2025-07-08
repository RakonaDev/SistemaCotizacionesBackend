<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\CotizacionGeneralController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/login", [UsuarioController::class, 'login']);

Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [UsuarioController::class, 'logout']);
    Route::get('/me', [UsuarioController::class,'me']);
    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
    Route::get('/dashboard/cotizaciones', [DashboardController::class,'traerCotizaciones']);

    Route::apiResource('clientes', ClientesController::class);
    Route::get('/buscarCliente', [ClientesController::class,'buscar']);
    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('cotizaciones', CotizacionController::class);
});

