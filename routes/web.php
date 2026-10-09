<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrabajadorController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'authenticate'])->name('login.post');

Route::get('/registro', [AuthController::class, 'register'])->name('register');
Route::post('/registro', [AuthController::class, 'store'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de recuperación de contraseña
Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

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
    Route::delete('/admin/configuracion/fechas-especiales/{fechaEspecial}', [\App\Http\Controllers\Admin\ConfiguracionController::class, 'destroyFechaEspecial'])->name('admin.configuracion.fechas_especiales.destroy');

    Route::get('/recepcion', function () {
        $swal = session('success') ? "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><script>document.addEventListener('DOMContentLoaded', ()=>Swal.fire({toast:true,position:'top-end',icon:'success',title:'".session('success')."',showConfirmButton:false,timer:3500,timerProgressBar:true,background:'#10b981',color:'#fff',iconColor:'#fff'}));</script>" : "";
        return $swal . "<h1>Panel de Recepcionista en construcción...</h1> <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Cerrar sesión</button></form>";
    })->name('recepcion.dashboard');

    Route::get('/trabajador', function () {
        $swal = session('success') ? "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><script>document.addEventListener('DOMContentLoaded', ()=>Swal.fire({toast:true,position:'top-end',icon:'success',title:'".session('success')."',showConfirmButton:false,timer:3500,timerProgressBar:true,background:'#10b981',color:'#fff',iconColor:'#fff'}));</script>" : "";
        return $swal . "<h1>Panel de Trabajador en construcción...</h1> <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Cerrar sesión</button></form>";
    })->name('trabajador.dashboard');

    Route::get('/cliente', function () {
        $swal = session('success') ? "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><script>document.addEventListener('DOMContentLoaded', ()=>Swal.fire({toast:true,position:'top-end',icon:'success',title:'".session('success')."',showConfirmButton:false,timer:3500,timerProgressBar:true,background:'#10b981',color:'#fff',iconColor:'#fff'}));</script>" : "";
        return $swal . "<h1>Perfil de Cliente en construcción...</h1> <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Cerrar sesión</button></form>";
    })->name('cliente.dashboard');
});
