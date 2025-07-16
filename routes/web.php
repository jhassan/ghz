<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandleController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/candles', [CandleController::class, 'index'])->name('candles.index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
