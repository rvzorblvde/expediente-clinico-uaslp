<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear los roles definidos
        Role::insert([
            ['idRol' => 1, 'nombreRol' => 'Responsable Sanitario'],
            ['idRol' => 2, 'nombreRol' => 'Trabajador'],
            ['idRol' => 3, 'nombreRol' => 'Alumno'],
        ]);

        // Crear el administrador
        User::create([
            'idRol' => 1,
            'nombres' => 'Tania',
            'apellido1' => 'Correa',
            'apellido2' => 'López',
            'correo' => 'admin@unifarmacia.com',

            /*
             * Laravel encripta por el cast hashed
             * Pero es buena práctica usar Hash::make() en los seeders
             * */

            'contrasena' => Hash::make('cambiar123'),
            'estado' => 'activo'
        ]);
    }
}
