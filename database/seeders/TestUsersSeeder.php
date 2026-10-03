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
                'name' => 'Admin',
                'apellidos' => 'Prueba',
                'telefono' => '1111111111',
                'email' => 'admin@bbs.com',
                'password' => Hash::make('Admin123!'),
                'role_id' => 1,
            ],
            [
                'name' => 'Recep',
                'apellidos' => 'Prueba',
                'telefono' => '2222222222',
                'email' => 'recepcion@bbs.com',
                'password' => Hash::make('Recep123!'),
                'role_id' => 2,
            ],
            [
                'name' => 'Barbero',
                'apellidos' => 'Prueba',
                'telefono' => '3333333333',
                'email' => 'barbero@bbs.com',
                'password' => Hash::make('Barbero123!'),
                'role_id' => 3,
            ],
            [
                'name' => 'Cliente',
                'apellidos' => 'Prueba',
                'telefono' => '4444444444',
                'email' => 'cliente@bbs.com',
                'password' => Hash::make('Cliente123!'),
                'role_id' => 4,
            ]
        ]);
    }
}