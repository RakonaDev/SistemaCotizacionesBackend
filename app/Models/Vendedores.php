<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedores extends Model
{
    protected $table = 'vendedores';
    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'direccion',
        'telefono',
    ];
}
