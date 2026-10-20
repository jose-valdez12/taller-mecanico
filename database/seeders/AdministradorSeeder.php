<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdministradorSeeder extends Seeder
{
    public function run(): void
    {
        $correo = env('ADMIN_EMAIL');
        $clave = env('ADMIN_PASSWORD');
        $nombre = env('ADMIN_NAME', 'Administrador General');

        if (empty($correo) || empty($clave)) {
            throw new RuntimeException(
                'Configura ADMIN_EMAIL y ADMIN_PASSWORD en el archivo .env.'
            );
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException(
                'ADMIN_EMAIL debe contener un correo válido.'
            );
        }

        if (strlen($clave) < 8) {
            throw new RuntimeException(
                'ADMIN_PASSWORD debe tener al menos 8 caracteres.'
            );
        }

        User::firstOrCreate(
            ['email' => $correo],
            [
                'name' => $nombre,
                'password' => Hash::make($clave),
                'rol' => 'administrador',
            ]
        );
    }
}
