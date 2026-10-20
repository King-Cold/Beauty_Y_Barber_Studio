<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Trabajador;
use App\Models\HorarioSucursal;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    /**
     * Muestra la Landing Page principal de Beauty & Barber Studio.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $servicios = Schema::hasTable('services')
            ? Service::where('is_active', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get()
            : collect();

        // Obtener categorías únicas presentes en los servicios activos
        $categorias = $servicios->pluck('category')
            ->filter()
            ->unique()
            ->values();

        // Obtener profesionales activos (Subtarea 4)
        // Se seleccionan únicamente datos públicos (nombre, apellidos) excluyendo datos confidenciales
        $trabajadores = Schema::hasTable('trabajadores')
            ? Trabajador::with([
                'user:id,name,apellidos',
                'serviciosActivos:id,name,category'
            ])
            ->activos()
            ->orderBy('id')
            ->get()
            : collect();

        // Obtener horarios semanales y estado en tiempo real (Subtarea 5)
        $horariosMap = HorarioSucursal::getHorariosMap();
        $ordenSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        $horariosSemana = [];
        foreach ($ordenSemana as $diaKey) {
            if (isset($horariosMap[$diaKey])) {
                $item = $horariosMap[$diaKey];
                $item['apertura_12h'] = $this->formatTime12h($item['apertura']);
                $item['cierre_12h']   = $this->formatTime12h($item['cierre']);
                $horariosSemana[$diaKey] = $item;
            }
        }

        $estadoSucursal = $this->calcularEstadoSucursal($horariosSemana);

        return view('landing', compact('servicios', 'categorias', 'trabajadores', 'horariosSemana', 'estadoSucursal'));
    }

    /**
     * Calcula el estado en tiempo real de apertura de la sucursal (Abierto o Cerrado).
     *
     * @param array $horariosSemana
     * @return array
     */
    public function calcularEstadoSucursal(array $horariosSemana): array
    {
        $tz = config('app.timezone') ?: 'America/Mexico_City';
        if ($tz === 'UTC') {
            $tz = env('APP_TIMEZONE', 'America/Mexico_City');
        }
        $now = \Illuminate\Support\Carbon::now($tz);
        $fechaHoy = $now->format('Y-m-d');
        $horaActual = $now->format('H:i');

        $mapaDias = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
        ];

        $diaHoyClave = $mapaDias[$now->dayOfWeek] ?? 'lunes';
        $configHoy = $horariosSemana[$diaHoyClave] ?? null;

        // Validar si hoy es fecha especial cerrada
        $esFechaEspecialCerrada = false;
        if (Schema::hasTable('fechas_especiales')) {
            $fe = \App\Models\FechaEspecial::whereDate('fecha', $fechaHoy)->first();
            if ($fe && $fe->cerrado_todo_el_dia) {
                $esFechaEspecialCerrada = true;
            }
        }

        // Evaluar si está abierto hoy en este momento
        $estaAbierto = false;
        if (!$esFechaEspecialCerrada && $configHoy && !empty($configHoy['abierto'])) {
            $apertura = substr($configHoy['apertura'], 0, 5);
            $cierre   = substr($configHoy['cierre'], 0, 5);

            if ($horaActual >= $apertura && $horaActual < $cierre) {
                $estaAbierto = true;
            }
        }

        if ($estaAbierto) {
            $cierre12h = $configHoy['cierre_12h'] ?? $this->formatTime12h($configHoy['cierre']);
            return [
                'abierto' => true,
                'texto'   => "Abierto ahora - Cierre a las {$cierre12h}",
                'dia_hoy' => $diaHoyClave,
            ];
        }

        // Si está cerrado en este momento, calcular el próximo día y hora de apertura
        // 1. Si hoy abre más tarde (la hora actual es antes de la apertura)
        if (!$esFechaEspecialCerrada && $configHoy && !empty($configHoy['abierto']) && $horaActual < substr($configHoy['apertura'], 0, 5)) {
            $apertura12h = $configHoy['apertura_12h'] ?? $this->formatTime12h($configHoy['apertura']);
            return [
                'abierto' => false,
                'texto'   => "Cerrado en este momento - Abrimos el {$configHoy['nombre']} a las {$apertura12h}",
                'dia_hoy' => $diaHoyClave,
            ];
        }

        // 2. Si hoy ya cerró o hoy no abre, buscar en los días siguientes
        $proximoDiaNombre = 'Lunes';
        $proximaHoraApertura = '09:00 AM';

        for ($i = 1; $i <= 7; $i++) {
            $fechaSiguiente = $now->copy()->addDays($i);
            $diaSiguienteClave = $mapaDias[$fechaSiguiente->dayOfWeek];
            $configSiguiente = $horariosSemana[$diaSiguienteClave] ?? null;

            $siguienteCerradoEspecial = false;
            if (Schema::hasTable('fechas_especiales')) {
                $feNext = \App\Models\FechaEspecial::whereDate('fecha', $fechaSiguiente->format('Y-m-d'))->first();
                if ($feNext && $feNext->cerrado_todo_el_dia) {
                    $siguienteCerradoEspecial = true;
                }
            }

            if (!$siguienteCerradoEspecial && $configSiguiente && !empty($configSiguiente['abierto'])) {
                $proximoDiaNombre = $configSiguiente['nombre'];
                $proximaHoraApertura = $configSiguiente['apertura_12h'] ?? $this->formatTime12h($configSiguiente['apertura']);
                break;
            }
        }

        return [
            'abierto' => false,
            'texto'   => "Cerrado en este momento - Abrimos el {$proximoDiaNombre} a las {$proximaHoraApertura}",
            'dia_hoy' => $diaHoyClave,
        ];
    }

    /**
     * Convierte una hora en formato militar 'HH:MM' a formato legible 12 horas 'hh:mm A'.
     *
     * @param string|null $time
     * @return string
     */
    public function formatTime12h(?string $time): string
    {
        if (empty($time)) {
            return '';
        }

        try {
            return \Illuminate\Support\Carbon::createFromFormat('H:i', substr(trim($time), 0, 5))->format('h:i A');
        } catch (\Throwable $e) {
            return (string)$time;
        }
    }

    /**
     * Gestiona la redirección del botón principal 'Agendar Cita'.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function agendarCita()
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()
                ->guest(route('login'))
                ->with('warning', 'Por favor, inicia sesión para agendar tu cita.')
                ->with('warning_title', '¡Bienvenido a Beauty & Barber Studio!');
        }

        $user = \Illuminate\Support\Facades\Auth::user();

        // Si es cliente (rol 4):
        if ((int)$user->role_id === 4) {
            return redirect()->route('cliente.agendamiento');
        }

        // Si es recepcionista (rol 2) o administrador (rol 1):
        if ((int)$user->role_id === 2 || (int)$user->role_id === 1) {
            return redirect()->route('recepcion.agendamiento');
        }

        // Si es trabajador (rol 3):
        return redirect()->route('trabajador.agenda');
    }
}
