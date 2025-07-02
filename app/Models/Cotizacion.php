<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'id_cotizacion_general',
        'gg',
        'utilidad',
        'costo_directo',
        'descripcion',
        'cantidad',
        'total_cotizaciones',
    ];

    public function cotizacionGeneral()
    {
        return $this->belongsTo(CotizacionGeneral::class, 'id_cotizacion_general');
    }

    public function detalles()
    {
        return $this->hasMany(CotizacionDetail::class, 'id_cotizacion');
    }
}
