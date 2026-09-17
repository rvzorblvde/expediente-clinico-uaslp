<?php

namespace Database\Seeders;

use App\Models\Expediente;
use Illuminate\Database\Seeder;

class ExpedienteSeeder extends Seeder
{
    public function run(): void
    {
        $expedientes = [
            ['nombres' => 'Juan Carlos', 'apellidos' => 'García López', 'fecha_nacimiento' => '2001-03-14', 'sexo' => 'hombre', 'curp' => 'GALJ010314HDFRPN02', 'peso' => 72.50, 'altura' => 1.75],
            ['nombres' => 'María Fernanda', 'apellidos' => 'Hernández Ruiz', 'fecha_nacimiento' => '2000-07-22', 'sexo' => 'mujer', 'curp' => 'HERM000722MDFRRR08', 'peso' => 58.30, 'altura' => 1.62],
            ['nombres' => 'Luis Alberto', 'apellidos' => 'Martínez Sánchez', 'fecha_nacimiento' => '1999-11-05', 'sexo' => 'hombre', 'curp' => 'MASL991105HDFRNS01', 'peso' => 80.10, 'altura' => 1.80],
            ['nombres' => 'Ana Sofía', 'apellidos' => 'Torres Jiménez', 'fecha_nacimiento' => '2002-01-30', 'sexo' => 'mujer', 'curp' => 'TOJA020130MDFRRN05', 'peso' => 55.00, 'altura' => 1.58],
            ['nombres' => 'Diego Armando', 'apellidos' => 'Flores Castillo', 'fecha_nacimiento' => '1998-09-12', 'sexo' => 'hombre', 'curp' => 'FOCD980912HDFRSG03', 'peso' => 90.40, 'altura' => 1.85],
            ['nombres' => 'Valentina', 'apellidos' => 'Reyes Morales', 'fecha_nacimiento' => '2003-05-18', 'sexo' => 'mujer', 'curp' => 'REMV030518MDFRRL07', 'peso' => 52.70, 'altura' => 1.60],
            ['nombres' => 'Alejandro', 'apellidos' => 'Cruz Ramírez', 'fecha_nacimiento' => '2000-12-25', 'sexo' => 'hombre', 'curp' => 'CURA001225HDFRMJ09', 'peso' => 68.90, 'altura' => 1.70],
            ['nombres' => 'Camila', 'apellidos' => 'Vázquez Ortiz', 'fecha_nacimiento' => '2001-08-08', 'sexo' => 'mujer', 'curp' => 'VAOC010808MDFZRM04', 'peso' => 60.20, 'altura' => 1.65],
            ['nombres' => 'Roberto', 'apellidos' => 'Gutiérrez Mendoza', 'fecha_nacimiento' => '1997-04-02', 'sexo' => 'hombre', 'curp' => 'GUMR970402HDFTNB06', 'peso' => 85.60, 'altura' => 1.78],
            ['nombres' => 'Alex', 'apellidos' => 'Domínguez Silva', 'fecha_nacimiento' => '2002-10-10', 'sexo' => 'otro', 'curp' => 'DOSA021010HDFMLL00', 'peso' => 64.00, 'altura' => 1.68],
        ];

        foreach ($expedientes as $data) {
            Expediente::firstOrCreate(['curp' => $data['curp']], $data);
        }
    }
}
