<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaDetail extends Model
{
    protected $table = 'proforma_detail';

    protected $fillable = [
        'descripcion',
        'UM',
        'cantidad',
        'precio_unit',
        'descuento',
        'total',
        'id_proforma',
    ];

    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'id_proforma');
    }

    public function incluye()
    {
        return $this->hasMany(ProformaIncluye::class, 'id_proforma_detail');
    }
}
