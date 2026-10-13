<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trabajador;
use App\Models\Service;
use App\Models\Cita;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestCitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener o crear al trabajador Alejandro Torres
        $alejandroUser = User::where('email', 'alejandro.barbero@bbs.com')->first();
        if (!$alejandroUser) {
            $alejandroUser = User::create([
                'name' => 'Alejandro',
                'apellidos' => 'Torres',
                'telefono' => '5554567890',
                'email' => 'alejandro.barbero@bbs.com',
                'password' => Hash::make('Barbero123!'),
                'role_id' => 3,
            ]);
        }

        $trabajador = Trabajador::firstOrCreate(
            ['user_id' => $alejandroUser->id],
            [
                'direccion' => 'Beauty & Barber Studio - Estación 1',
                'experiencia' => 4,
                'activo' => true,
                'horario' => [
                    'lunes' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                    'martes' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                    'miercoles' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                    'jueves' => ['inicio' => '09:00', 'fin' => '19:00', 'laboral' => true],
                    'viernes' => ['inicio' => '09:00', 'fin' => '20:00', 'laboral' => true],
                    'sabado' => ['inicio' => '09:00', 'fin' => '20:00', 'laboral' => true],
                    'domingo' => ['laboral' => false],
                ],
            ]
        );

        // 2. Vincular servicios al trabajador
        $services = Service::where('is_active', true)->get();
        if ($services->isNotEmpty()) {
            $trabajador->servicios()->sync($services->pluck('id'));
        }

        // 3. Obtener o crear clientes de prueba
        $clienteRoberto = User::where('email', 'roberto.cliente@gmail.com')->first();
        if (!$clienteRoberto) {
            $clienteRoberto = User::create([
                'name' => 'Roberto',
                'apellidos' => 'Sánchez',
                'telefono' => '5556543210',
                'email' => 'roberto.cliente@gmail.com',
                'password' => Hash::make('Cliente123!'),
                'role_id' => 4,
            ]);
        }

        $clienteMariana = User::firstOrCreate(
            ['email' => 'mariana.cliente@gmail.com'],
            [
                'name' => 'Mariana',
                'apellidos' => 'Ruiz Morales',
                'telefono' => '5557891234',
                'password' => Hash::make('Cliente123!'),
                'role_id' => 4,
            ]
        );

        $clienteDaniel = User::firstOrCreate(
            ['email' => 'daniel.cliente@gmail.com'],
            [
                'name' => 'Daniel',
                'apellidos' => 'Navarro Ríos',
                'telefono' => '5553216549',
                'password' => Hash::make('Cliente123!'),
                'role_id' => 4,
            ]
        );

        // 4. Crear citas de prueba variadas
        $hoy = Carbon::today()->format('Y-m-d');
        $manana = Carbon::tomorrow()->format('Y-m-d');
        $ayer = Carbon::yesterday()->format('Y-m-d');

        $servicioCorte = $services->firstWhere('id', 1) ?? $services->first();
        $servicioFade = $services->firstWhere('id', 2) ?? $services->first();
        $servicioBarba = $services->firstWhere('id', 4) ?? $services->first();
        $servicioToalla = $services->firstWhere('id', 3) ?? $services->first();

        // Cita 1: Hoy - En atención (10:00 a 10:45)
        Cita::firstOrCreate(
            [
                'trabajador_id' => $trabajador->id,
                'fecha' => $hoy,
                'hora_inicio' => '10:00',
            ],
            [
                'user_id' => $clienteRoberto->id,
                'service_id' => $servicioCorte->id,
                'hora_fin' => '10:45',
                'duracion_minutos' => 45,
                'precio' => $servicioCorte->price ?? 250.00,
                'estado' => 'en_atencion',
                'notas' => 'Cliente solicita corte clásico manteniendo longitud superior y desvanecido pulido en laterales.',
            ]
        );

        // Cita 2: Hoy - Confirmada (12:00 a 12:50)
        Cita::firstOrCreate(
            [
                'trabajador_id' => $trabajador->id,
                'fecha' => $hoy,
                'hora_inicio' => '12:00',
            ],
            [
                'user_id' => $clienteDaniel->id,
                'service_id' => $servicioFade->id,
                'hora_fin' => '12:50',
                'duracion_minutos' => 50,
                'precio' => $servicioFade->price ?? 280.00,
                'estado' => 'confirmada',
                'notas' => 'Corte Fade alto a navaja. El cliente tiene piel sensible.',
            ]
        );

        // Cita 3: Hoy - Confirmada (16:00 a 16:30)
        Cita::firstOrCreate(
            [
                'trabajador_id' => $trabajador->id,
                'fecha' => $hoy,
                'hora_inicio' => '16:00',
            ],
            [
                'user_id' => $clienteRoberto->id,
                'service_id' => $servicioBarba->id,
                'hora_fin' => '16:30',
                'duracion_minutos' => 30,
                'precio' => $servicioBarba->price ?? 180.00,
                'estado' => 'confirmada',
                'notas' => 'Perfilado de barba con ritual de toalla y loción refrescante.',
            ]
        );

        // Cita 4: Mañana - Confirmada (11:00 a 11:40)
        Cita::firstOrCreate(
            [
                'trabajador_id' => $trabajador->id,
                'fecha' => $manana,
                'hora_inicio' => '11:00',
            ],
            [
                'user_id' => $clienteMariana->id,
                'service_id' => $servicioToalla->id,
                'hora_fin' => '11:40',
                'duracion_minutos' => 40,
                'precio' => $servicioToalla->price ?? 220.00,
                'estado' => 'confirmada',
                'notas' => 'Cita reservada desde la plataforma web.',
            ]
        );

        // Cita 5: Ayer - Completada (15:00 a 15:45)
        Cita::firstOrCreate(
            [
                'trabajador_id' => $trabajador->id,
                'fecha' => $ayer,
                'hora_inicio' => '15:00',
            ],
            [
                'user_id' => $clienteDaniel->id,
                'service_id' => $servicioCorte->id,
                'hora_fin' => '15:45',
                'duracion_minutos' => 45,
                'precio' => $servicioCorte->price ?? 250.00,
                'estado' => 'completada',
                'notas' => 'Servicio concluido satisfactoriamente.',
                'observaciones' => 'Se aplicó pomada mate base agua tras el corte. El cliente prefiere peinado lateral hacia la izquierda con volumen sutil. Recomendado peine texturizador para su próxima cita.',
            ]
        );
    }
}
