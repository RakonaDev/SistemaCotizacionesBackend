<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaIncluye extends Model
{
    protected $table = 'proforma_incluye';

    protected $fillable = [
        'nombre',
        'id_proforma_detail',
    ];

    public function detalle()
    {
        return $this->belongsTo(ProformaDetail::class, 'id_proforma_detail');
    }
}
