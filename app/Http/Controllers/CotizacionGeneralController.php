<?php

namespace App\Http\Controllers;

use App\Models\CotizacionGeneral;
use Illuminate\Http\Request;

class CotizacionGeneralController extends Controller
{
    public function index()
    {
        $cotizaciones = CotizacionGeneral::with([
            'cliente',
            'cotizaciones.detalles.servicio',
            'cotizaciones.detalles.cajas'
        ])->get();

        return response()->json($cotizaciones);
    }
}
