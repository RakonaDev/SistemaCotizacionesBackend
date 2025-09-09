<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    protected $fillable = [
        'asunto',
        'codigo',
        'lugar_entrega',
        'forma_pago',
        'moneda',
        'subtotal',
        'fecha_inicial',
        'fecha_entrega',
        'dias',
        'descuento',
        'valor_venta',
        'igv',
        'pdf',
        'importe_total',
        'id_cliente',
        'id_vendedor',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedores::class, 'id_vendedor');
    }

    public function detalles()
    {
        return $this->hasMany(ProformaDetail::class, 'id_proforma');
    }
}
