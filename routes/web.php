<?php

use Illuminate\Support\Facades\Route;

// RUTAS PARA SITIO WEB
Route::get('/', function () {
    return view('welcome');
});

Route::get('php-info', function() {
    phpinfo();
});

// RUTAS SISTEMA
Route::group(['prefix' => 'central'], function() {
    Route::livewire('/', 'pages::app.dashboard')->name('home');

    Route::group(['prefix' => 'clientes'], function() {
        Route::livewire('/', 'pages::app.clientes.index')->name('clientes');
    });
    Route::livewire('/instancias', 'pages::app.instancias.index')->name('instancias');
});
