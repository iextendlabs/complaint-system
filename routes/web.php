<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('');
Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/complaints/export', [App\Http\Controllers\HomeController::class, 'export'])->middleware('auth')->name('complaints.export');
Route::post('/complaints/{complaint}/update-status', [App\Http\Controllers\HomeController::class, 'updateStatus'])->middleware('auth')->name('complaints.updateStatus');
