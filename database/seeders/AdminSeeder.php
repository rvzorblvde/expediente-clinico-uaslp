<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'responsable@admin.edu.mx'],
            [
                'name' => 'Responsable Sanitario',
                'password' => Hash::make('cambiar123'), // cámbialo luego
                'role' => User::ROLE_RESPONSABLE,
            ]
        );

        $this->call(AdminSeeder::class);
    }
}
