<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionGeneral extends Model
{
    protected $table = 'cotizaciones_generales';

    protected $fillable = [
        'fecha',
        'dias_entrega',
        'descripcion',
        'monto_total',
        'id_cliente',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_cotizacion_general');
    }
}
