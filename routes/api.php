<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::post('/complaints', [HomeController::class, 'store'])->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);
