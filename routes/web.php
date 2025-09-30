<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/', function () {
    return view('welcome');
})->name('');

Route::get('/admin', [HomeController::class, 'index'])->name('home');
Route::get('/complaints/export', [HomeController::class, 'export'])->middleware('auth')->name('complaints.export');

Route::delete('/complaints/{complaint}', [HomeController::class, 'destroy'])->middleware('auth')->name('complaints.destroy');
Route::post('/complaints/{complaint}/update-status', [HomeController::class, 'updateStatus'])->middleware('auth')->name('complaints.updateStatus');

Route::get('/tracking', [TrackingController::class, 'track'])->name('tracking.get');
