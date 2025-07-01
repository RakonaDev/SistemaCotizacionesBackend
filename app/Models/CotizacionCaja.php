<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionCaja extends Model
{
    protected $table = 'cotizacion_caja';

    protected $fillable = [
        'id_cotizacion_detail',
        'total',
        'cantidad',
        'precio_unitario',
        'horas_habiles',
        'hora_habil_costo',
    ];

    public function detalle()
    {
        return $this->belongsTo(CotizacionDetail::class, 'id_cotizacion_detail');
    }
}
