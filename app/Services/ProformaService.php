<?php

namespace App\Services;

use App\Models\Proforma;

class ProformaService
{
    public function generarCodigo()
    {
        $ultimaProforma = Proforma::orderBy('id', 'desc')->first();
        $numero = 4000 + ($ultimaProforma ? $ultimaProforma->id + 1 : 0);
        $numeroFormateado = str_pad($numero, 8, '0', STR_PAD_LEFT);

        // Retornar con prefijo
        return "003-" . $numeroFormateado;
    }
}
