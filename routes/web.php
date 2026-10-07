<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrabajadorController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'authenticate'])->name('login.post');

Route::get('/registro', [AuthController::class, 'register'])->name('register');
Route::post('/registro', [AuthController::class, 'store'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Rutas de administración de servicios
    Route::get('/admin/servicios', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services');
    Route::post('/admin/servicios', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('admin.services.store');
    Route::put('/admin/servicios/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('admin.services.update');
    Route::patch('/admin/servicios/{service}/toggle-status', [\App\Http\Controllers\Admin\ServiceController::class, 'toggleStatus'])->name('admin.services.toggle_status');

    // Rutas de administración de trabajadores
    Route::get('/admin/trabajadores', [TrabajadorController::class, 'index'])->name('admin.trabajadores.index');
    Route::post('/admin/trabajadores', [TrabajadorController::class, 'store'])->name('admin.trabajadores.store');
    Route::get('/admin/trabajadores/{trabajador}', [TrabajadorController::class, 'show'])->name('admin.trabajadores.show');
    Route::put('/admin/trabajadores/{trabajador}', [TrabajadorController::class, 'update'])->name('admin.trabajadores.update');
    Route::get('/admin/trabajadores/{trabajador}/servicios', [TrabajadorController::class, 'getServices'])->name('admin.trabajadores.services');
    Route::post('/admin/trabajadores/{trabajador}/servicios', [TrabajadorController::class, 'syncServices'])->name('admin.trabajadores.sync_services');

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
