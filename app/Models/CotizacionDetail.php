<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetail extends Model
{
    protected $table = 'cotizacion_detail';

    protected $fillable = [
        'descripcion',
        'id_servicio',
        'id_cotizacion',
        'precio_total',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion');
    }

    public function cajas()
    {
        return $this->hasMany(CotizacionCaja::class, 'id_cotizacion_detail');
    }
}
