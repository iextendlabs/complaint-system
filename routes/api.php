<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpController;

Route::post('/complaints', [HomeController::class, 'store'])->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);

Route::post('otps', [OtpController::class, 'store'])->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);
Route::post('otps/verify', [OtpController::class, 'verifyOtp'])->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);

