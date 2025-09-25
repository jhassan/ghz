<?php

use App\Http\Controllers\CandleController;
use App\Http\Controllers\CoinsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/candles', [CandleController::class, 'index'])->name('candles.index');

Route::get('/coins', [CoinsController::class, 'edit'])->name('coins.edit');
Route::post('/coins', [CoinsController::class, 'update'])->name('coins.update');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
