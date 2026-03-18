<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ADMINISTRADOR (Acceso total a configuración y gestión de usuarios)
        User::create([
            'name'            => 'Administrador del Sistema',
            'email'           => 'admin@imperium.org.pe',
            'document_type'   => 'DNI',
            'document_number' => '00000000',
            'role'            => 'admin',
            'password'        => Hash::make('admin123'), // Cambiar en producción
            'is_active'       => true,
        ]);

        // 2. OPERADOR (Personal de Imperium que envía las notificaciones)
        User::create([
            'name'            => 'Operador SIREA 01',
            'email'           => 'operador@imperium.org.pe',
            'document_type'   => 'DNI',
            'document_number' => '11111111',
            'role'            => 'operador',
            'password'        => Hash::make('operador123'),
            'is_active'       => true,
        ]);

        // 3. USUARIO FINAL (Sujeto Procesal / Cliente)
        // Este usuario tendrá asociada una casilla electrónica
        User::create([
            'name'            => 'Juan Pérez - Sujeto Procesal',
            'email'           => 'jperez@gmail.com',
            'document_type'   => 'DNI',
            'document_number' => '44556677',
            'role'            => 'usuario_final',
            'password'        => Hash::make('usuario123'),
            'is_active'       => true,
        ]);
    }
}
