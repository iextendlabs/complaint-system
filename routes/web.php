
<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Auth::routes();

Route::group(['middleware' => ['auth', 'role:admin|staff']], function () {
    Route::get('/admin', [HomeController::class, 'index'])->name('home');
    Route::get('/complaints/export', [HomeController::class, 'export'])->name('complaints.export');
    Route::delete('/complaints/{complaint}', [HomeController::class, 'destroy'])->name('complaints.destroy');
    Route::post('/complaints/{complaint}/update-status', [HomeController::class, 'updateStatus'])->name('complaints.updateStatus');
    Route::get('/complaints/{complaint}', [HomeController::class, 'complaintShow'])->name('complaints.show');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/', [TrackingController::class, 'track'])->name('tracking.get');
