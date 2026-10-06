<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'authenticate'])->name('login.post');

Route::get('/registro', [AuthController::class, 'register'])->name('register');
Route::post('/registro', [AuthController::class, 'store'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

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
