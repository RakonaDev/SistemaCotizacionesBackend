<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'email' => 'administrador@cotizaciones.com',
            'password' => Hash::make('Sistemas2025'),
            'name' => 'Administrador'
        ]);

        Servicio::create([
            'nombre' => 'COMPRA DE MATERIALES E INSUMOS',
            'tipo' => 'SERVICIO'
        ]);
        Servicio::create([
            'nombre' => 'AREA DE MECANIZADO',
            'tipo' => 'AREA'
        ]);

        Cliente::create([
            'nombre' => 'Juan Carlos Cajas',
            'direccion' => 'MZ C LT 14',
            'ruc' => '98231823',
            'correo' => 'juancajas1905@gmail.com',
            'telefono' => 982385738,
        ]);
    }
}
