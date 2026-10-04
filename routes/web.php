<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::get('/registro', function () {
    return view('auth.register');
})->name('register');

