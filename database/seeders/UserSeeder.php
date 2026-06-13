<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Milagros barrios',
                'email' => 'mili@nada.com',
                'telefono' => '541144445555',
                'tipo_rol' => 'emp',
                'activo' => true,
            ],
            [
                'name' => 'Juan Carlos Barrios Gonzalez',
                'email' => 'barriosjc@yahoo.com.ar',
                'telefono' => '+541152203659',
                'tipo_rol' => 'emp',
                'activo' => true,
            ],
            [
                'name' => 'Juarez Camila',
                'email' => 'nada@com.ar',
                'telefono' => '541155556666',
                'tipo_rol' => 'emp',
                'activo' => true,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'telefono' => '999888777',
                'tipo_rol' => 'sup',
                'activo' => true,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
