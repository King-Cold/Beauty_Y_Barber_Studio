<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Insertar los roles en la base de datos
        DB::table('roles')->insert([
            ['id' => 1, 'nombre' => 'Administrador'],
            ['id' => 2, 'nombre' => 'Recepcionista'],
            ['id' => 3, 'nombre' => 'Trabajador'],
            ['id' => 4, 'nombre' => 'Cliente'],
        ]);

        // 2. Insertar los usuarios de prueba vinculados a su rol
        DB::table('users')->insert([
            [
                'name' => 'Carlos',
                'apellidos' => 'Mendoza',
                'telefono' => '5551234567',
                'email' => 'carlos.admin@bbs.com',
                'password' => Hash::make('
                '),
                'role_id' => 1,
            ],
            [
                'name' => 'Laura',
                'apellidos' => 'García',
                'telefono' => '5559876543',
                'email' => 'laura.recepcion@bbs.com',
                'password' => Hash::make('Recep123!'),
                'role_id' => 2,
            ],
            [
                'name' => 'Alejandro',
                'apellidos' => 'Torres',
                'telefono' => '5554567890',
                'email' => 'alejandro.barbero@bbs.com',
                'password' => Hash::make('Barbero123!'),
                'role_id' => 3,
            ],
            [
                'name' => 'Roberto',
                'apellidos' => 'Sánchez',
                'telefono' => '5556543210',
                'email' => 'roberto.cliente@gmail.com',
                'password' => Hash::make('Cliente123!'),
                'role_id' => 4,
            ]
        ]);
    }
}