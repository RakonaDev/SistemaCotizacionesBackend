<?php

namespace App\Http\Controllers;

use App\Models\CotizacionGeneral;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function resumen()
    {
        $now = Carbon::now();
        $mesActual = $now->month;
        $anioActual = $now->year;

        $mesAnterior = $now->copy()->subMonth()->month;
        $anioAnterior = $now->copy()->subMonth()->year;

        // 🔹 Cotizaciones Totales
        $totalActual = CotizacionGeneral::whereYear('created_at', $anioActual)
            ->whereMonth('created_at', $mesActual)
            ->count();

        $totalAnterior = CotizacionGeneral::whereYear('created_at', $anioAnterior)
            ->whereMonth('created_at', $mesAnterior)
            ->count();

        $porcentajeTotal = $this->calcularPorcentaje($totalAnterior, $totalActual);

        // 🔹 Cotizaciones Pendientes (simulación con fecha_final nula)
        $pendientesActual = CotizacionGeneral::whereNull('fecha_final')
            ->whereYear('created_at', $anioActual)
            ->whereMonth('created_at', $mesActual)
            ->count();

        $pendientesAnterior = CotizacionGeneral::whereNull('fecha_final')
            ->whereYear('created_at', $anioAnterior)
            ->whereMonth('created_at', $mesAnterior)
            ->count();

        $porcentajePendientes = $this->calcularPorcentaje($pendientesAnterior, $pendientesActual);

        // 🔹 Clientes Atendidos
        $clientesActual = CotizacionGeneral::whereYear('created_at', $anioActual)
            ->whereMonth('created_at', $mesActual)
            ->distinct('id_cliente')
            ->count('id_cliente');

        $clientesAnterior = CotizacionGeneral::whereYear('created_at', $anioAnterior)
            ->whereMonth('created_at', $mesAnterior)
            ->distinct('id_cliente')
            ->count('id_cliente');

        $porcentajeClientes = $this->calcularPorcentaje($clientesAnterior, $clientesActual);

        // 🔹 Valor Total
        $valorActual = CotizacionGeneral::whereYear('created_at', $anioActual)
            ->whereMonth('created_at', $mesActual)
            ->sum('monto_total');

        $valorAnterior = CotizacionGeneral::whereYear('created_at', $anioAnterior)
            ->whereMonth('created_at', $mesAnterior)
            ->sum('monto_total');

        $porcentajeValor = $this->calcularPorcentaje($valorAnterior, $valorActual);

        // 🔹 Respuesta Final
        return response()->json([
            [
                'title' => 'Cotizaciones Totales',
                'value' => number_format($totalActual),
                'change' => $porcentajeTotal['porcentaje'],
                'trend' => $porcentajeTotal['trend'],
            ],
            [
                'title' => 'Cotizaciones Pendientes',
                'value' => number_format($pendientesActual),
                'change' => $porcentajePendientes['porcentaje'],
                'trend' => $porcentajePendientes['trend'],
            ],
            [
                'title' => 'Clientes Atendidos',
                'value' => number_format($clientesActual),
                'change' => $porcentajeClientes['porcentaje'],
                'trend' => $porcentajeClientes['trend'],
            ],
            [
                'title' => 'Valor Total',
                'value' => '$' . number_format($valorActual / 1_000_000, 1),
                'change' => $porcentajeValor['porcentaje'],
                'trend' => $porcentajeValor['trend'],
            ],
        ]);
    }

    public function traerCotizaciones()
    {
        $cotizacionGeneral = CotizacionGeneral::with([
            'cliente',
            'cotizaciones.detalles.cajas',
            'cotizaciones.detalles.servicio'
        ])->get()->take(5);

        return response()->json($cotizacionGeneral);
    }

    private function calcularPorcentaje($anterior, $actual)
    {
        if ($anterior == 0) {
            if ($actual == 0) {
                return [
                    'porcentaje' => '0%',
                    'trend' => 'neutral',
                ];
            }
            return [
                'porcentaje' => '+100%',
                'trend' => 'up',
            ];
        }

        $cambio = (($actual - $anterior) / $anterior) * 100;
        $trend = $cambio >= 0 ? 'up' : 'down';
        $porcentaje = ($cambio >= 0 ? '+' : '') . round($cambio) . '%';

        return [
            'porcentaje' => $porcentaje,
            'trend' => $trend,
        ];
    }
}
