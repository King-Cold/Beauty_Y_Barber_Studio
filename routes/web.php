<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\LandingController;

// ==========================================
// RUTA PRINCIPAL: LANDING PAGE PÚBLICA
// ==========================================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/agendar-cita', [LandingController::class, 'agendarCita'])->name('landing.agendar');

// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/', [AuthController::class, 'authenticate']);

Route::get('/registro', [AuthController::class, 'register'])->name('register');
Route::post('/registro', [AuthController::class, 'store'])->name('register.post');

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de recuperación de contraseña
Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');

Route::middleware('auth')->group(function () {
    // ==========================================
    // RUTAS PARA ADMINISTRADORES (Rol 1)
    // ==========================================
    Route::middleware(['role:1'])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Gestión de usuarios
        Route::get('/admin/usuarios', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.usuarios');
        Route::post('/admin/usuarios', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.usuarios.store');
        Route::match(['PUT', 'POST'], '/admin/usuarios/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.usuarios.update');
        Route::delete('/admin/usuarios/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.usuarios.destroy');

        // Rutas de administración de servicios
        Route::get('/admin/servicios', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services');
        Route::post('/admin/servicios', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('admin.services.store');
        Route::put('/admin/servicios/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.services.update');
        Route::patch('/admin/servicios/{service}/toggle-status', [\App\Http\Controllers\Admin\ServiceController::class, 'toggleStatus'])->name('admin.services.toggle_status');
        Route::delete('/admin/servicios/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('admin.services.destroy');

        // Rutas de administración de trabajadores
        Route::get('/admin/trabajadores', [TrabajadorController::class, 'index'])->name('admin.trabajadores.index');
        Route::post('/admin/trabajadores', [TrabajadorController::class, 'store'])->name('admin.trabajadores.store');
        Route::get('/admin/trabajadores/{trabajador}', [TrabajadorController::class, 'show'])->name('admin.trabajadores.show');
        Route::put('/admin/trabajadores/{trabajador}', [TrabajadorController::class, 'update'])->name('admin.trabajadores.update');
        Route::get('/admin/trabajadores/{trabajador}/servicios', [TrabajadorController::class, 'getServices'])->name('admin.trabajadores.services');
        Route::post('/admin/trabajadores/{trabajador}/servicios', [TrabajadorController::class, 'syncServices'])->name('admin.trabajadores.sync_services');
        Route::get('/admin/trabajadores/{trabajador}/horario', [TrabajadorController::class, 'getSchedule'])->name('admin.trabajadores.horario.get');
        Route::post('/admin/trabajadores/{trabajador}/horario', [TrabajadorController::class, 'saveSchedule'])->name('admin.trabajadores.horario.save');
        Route::get('/admin/trabajadores/{trabajador}/disponibilidad', [TrabajadorController::class, 'getDisponibilidad'])->name('admin.trabajadores.disponibilidad');
        Route::patch('/admin/trabajadores/{trabajador}/toggle-status', [TrabajadorController::class, 'toggleStatus'])->name('admin.trabajadores.toggle_status');
        Route::delete('/admin/trabajadores/{trabajador}', [TrabajadorController::class, 'destroy'])->name('admin.trabajadores.destroy');

        // Configuración general y fechas especiales
        Route::get('/admin/configuracion', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'index'])->name('admin.configuracion');
        Route::post('/admin/configuracion/horarios', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'saveBranchSchedule'])->name('admin.configuracion.horarios.save');
        Route::get('/admin/configuracion/fechas-especiales', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'getFechasEspeciales'])->name('admin.configuracion.fechas_especiales.index');
        Route::post('/admin/configuracion/fechas-especiales', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'storeFechaEspecial'])->name('admin.configuracion.fechas_especiales.store');
        Route::put('/admin/configuracion/fechas-especiales/{fechaEspecial}', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'updateFechaEspecial'])->name('admin.configuracion.fechas_especiales.update');
        Route::delete('/admin/configuracion/fechas-especiales/{fechaEspecial}', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'destroyFechaEspecial'])->name('admin.configuracion.fechas_especiales.destroy');
        // Consulta y Visualización de Citas y Calendario para Administrador (Solo lectura / Auditoría)
        Route::get('/admin/citas', [\App\Http\Controllers\Admin\CitaAdminController::class, 'index'])->name('admin.citas');
        Route::get('/admin/calendario', [\App\Http\Controllers\Admin\CitaAdminController::class, 'calendario'])->name('admin.calendario');
        Route::get('/admin/calendario/eventos', [\App\Http\Controllers\Admin\CitaAdminController::class, 'obtenerCitasCalendario'])->name('admin.calendario.eventos');
        Route::get('/admin/citas/{cita}/detalle', [\App\Http\Controllers\Admin\CitaAdminController::class, 'detalleCita'])->name('admin.citas.detalle');
        // Operaciones de mutación sobre citas bloqueadas con 403 para Administrador (Solo lectura)
        Route::patch('/admin/citas/{cita}/estado', [\App\Http\Controllers\Admin\CitaAdminController::class, 'actualizarEstadoCita'])->name('admin.citas.actualizar-estado');
        Route::match(['put', 'patch'], '/admin/citas/{cita}', [\App\Http\Controllers\Admin\CitaAdminController::class, 'actualizarCita'])->name('admin.citas.update');
        Route::match(['post', 'patch'], '/admin/citas/{cita}/cancelar', [\App\Http\Controllers\Admin\CitaAdminController::class, 'cancelarCita'])->name('admin.citas.cancelar');
        Route::match(['post', 'patch'], '/admin/citas/{cita}/confirmar', [\App\Http\Controllers\Admin\CitaAdminController::class, 'confirmarCita'])->name('admin.citas.confirmar');
        Route::post('/admin/citas/{cita}/checkin', [\App\Http\Controllers\Admin\CitaAdminController::class, 'checkInCita'])->name('admin.citas.checkin');

        // Perfil y credenciales del Administrador
        Route::get('/admin/perfil', [\App\Http\Controllers\Admin\AdminProfileController::class, 'perfil'])->name('admin.perfil');
        Route::put('/admin/perfil/datos', [\App\Http\Controllers\Admin\AdminProfileController::class, 'actualizarDatos'])->name('admin.perfil.datos');
        Route::put('/admin/perfil/password', [\App\Http\Controllers\Admin\AdminProfileController::class, 'actualizarPassword'])->name('admin.perfil.password');
    });

    // ==========================================
    // RUTAS PARA RECEPCIONISTAS (Rol 2)
    // ==========================================
    Route::middleware(['role:1,2'])->prefix('recepcion')->name('recepcion.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RecepcionController::class, 'index'])->name('dashboard');
        Route::get('/citas', [\App\Http\Controllers\RecepcionController::class, 'citas'])->name('citas');
        Route::get('/agendamiento', [\App\Http\Controllers\RecepcionController::class, 'agendamiento'])->name('agendamiento');
        Route::get('/agendamiento/buscar-clientes', [\App\Http\Controllers\RecepcionController::class, 'buscarClientes'])->name('agendamiento.buscar-clientes');
        Route::get('/agendamiento/disponibilidad', [\App\Http\Controllers\RecepcionController::class, 'consultarDisponibilidad'])->name('agendamiento.disponibilidad');
        Route::post('/agendamiento/validar-horario', [\App\Http\Controllers\RecepcionController::class, 'validarHorario'])->name('agendamiento.validar-horario');
        Route::post('/agendamiento/confirmar', [\App\Http\Controllers\RecepcionController::class, 'confirmarCita'])->name('agendamiento.confirmar');
        Route::post('/turnos/registrar', [\App\Http\Controllers\RecepcionController::class, 'registrarTurnoRecepcion'])->name('turnos.registrar');
        Route::get('/calendario', [\App\Http\Controllers\RecepcionController::class, 'calendario'])->name('calendario');
        Route::get('/calendario/eventos', [\App\Http\Controllers\RecepcionController::class, 'obtenerCitasCalendario'])->name('calendario.eventos');
        Route::get('/citas/{cita}/detalle', [\App\Http\Controllers\RecepcionController::class, 'detalleCita'])->name('citas.detalle');
        Route::patch('/citas/{cita}/estado', [\App\Http\Controllers\RecepcionController::class, 'actualizarEstadoCita'])->name('citas.actualizar-estado');
        Route::match(['put', 'patch'], '/citas/{cita}', [\App\Http\Controllers\RecepcionController::class, 'actualizarCita'])->name('citas.update');
        Route::match(['post', 'patch'], '/citas/{cita}/cancelar', [\App\Http\Controllers\RecepcionController::class, 'cancelarCita'])->name('citas.cancelar');
        Route::match(['post', 'patch'], '/citas/{cita}/confirmar', [\App\Http\Controllers\RecepcionController::class, 'confirmarCitaDirecta'])->name('citas.confirmar');
        Route::post('/citas/{cita}/checkin', [\App\Http\Controllers\RecepcionController::class, 'checkInCita'])->name('citas.checkin');
        Route::get('/clientes', [\App\Http\Controllers\RecepcionController::class, 'clientes'])->name('clientes');
        Route::get('/clientes/buscar', [\App\Http\Controllers\RecepcionController::class, 'buscarClientes'])->name('clientes.buscar');
        Route::post('/clientes/registrar-rapido', [\App\Http\Controllers\RecepcionController::class, 'registrarClienteRapido'])->name('clientes.registrar-rapido');

        // Gestión de Perfil y Seguridad de Recepción (Rol 2)
        Route::get('/perfil', [\App\Http\Controllers\RecepcionController::class, 'perfil'])->name('perfil');
        Route::put('/perfil/datos', [\App\Http\Controllers\RecepcionController::class, 'actualizarDatos'])->name('perfil.datos');
        Route::put('/perfil/password', [\App\Http\Controllers\RecepcionController::class, 'actualizarPassword'])->name('perfil.password');
    });

    // ==========================================
    // RUTAS PARA TRABAJADORES (Rol 3)
    // ==========================================
    Route::middleware(['role:1,3'])->prefix('trabajador')->name('trabajador.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TrabajadorPanelController::class, 'dashboard'])->name('dashboard');
        Route::get('/agenda', [\App\Http\Controllers\TrabajadorPanelController::class, 'agenda'])->name('agenda');
        Route::get('/configuracion', [\App\Http\Controllers\TrabajadorPanelController::class, 'configuracion'])->name('configuracion');
        Route::put('/configuracion/datos', [\App\Http\Controllers\TrabajadorPanelController::class, 'actualizarDatos'])->name('configuracion.datos');
        Route::put('/configuracion/password', [\App\Http\Controllers\TrabajadorPanelController::class, 'actualizarPassword'])->name('configuracion.password');
        Route::get('/citas/{cita}', [\App\Http\Controllers\TrabajadorPanelController::class, 'showCita'])->name('citas.show');
        Route::patch('/citas/{cita}/estado', [\App\Http\Controllers\TrabajadorPanelController::class, 'actualizarEstado'])->name('citas.actualizar_estado');
        Route::post('/citas/{cita}/observaciones', [\App\Http\Controllers\TrabajadorPanelController::class, 'guardarObservaciones'])->name('citas.observaciones');
    });

    // ==========================================
    // RUTAS PARA CLIENTES (Rol 4)
    // ==========================================
    Route::middleware(['role:1,4'])->prefix('cliente')->name('cliente.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClienteController::class, 'index'])->name('dashboard');
        Route::get('/agendamiento', [\App\Http\Controllers\ClienteController::class, 'agendamiento'])->name('agendamiento');
        Route::get('/agendamiento/disponibilidad', [\App\Http\Controllers\ClienteController::class, 'consultarDisponibilidad'])->name('agendamiento.disponibilidad');
        Route::post('/agendamiento/validar-horario', [\App\Http\Controllers\ClienteController::class, 'validarHorario'])->name('agendamiento.validar-horario');
        Route::post('/agendamiento/confirmar', [\App\Http\Controllers\ClienteController::class, 'confirmarCita'])->name('agendamiento.confirmar');
        Route::post('/agendamiento', [\App\Http\Controllers\ClienteController::class, 'confirmarCita'])->name('agendamiento.store');
        Route::get('/historial', [\App\Http\Controllers\ClienteController::class, 'historial'])->name('historial');
        Route::get('/citas', [\App\Http\Controllers\ClienteController::class, 'historial'])->name('citas.index');
        Route::get('/citas/{cita}', [\App\Http\Controllers\ClienteController::class, 'showCita'])->name('citas.show');
        Route::match(['post', 'patch'], '/citas/{cita}/cancelar', [\App\Http\Controllers\ClienteController::class, 'cancelarCita'])->name('citas.cancelar');

        // Rutas de gestión de Perfil y Seguridad del Cliente
        Route::get('/perfil', [\App\Http\Controllers\ClienteController::class, 'perfil'])->name('perfil');
        Route::put('/perfil/datos', [\App\Http\Controllers\ClienteController::class, 'actualizarDatos'])->name('perfil.datos');
        Route::put('/perfil/password', [\App\Http\Controllers\ClienteController::class, 'actualizarPassword'])->name('perfil.password');
    });
});
