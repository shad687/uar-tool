<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UARController;
use App\Http\Controllers\UARFileController;
use App\Models\UAR;
use App\Http\Controllers\UARUserController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [UARController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/uar/create', [UARController::class, 'create'])->name('uar.create');
Route::post('/uar/store', [UARController::class, 'store'])->name('uar.store'); // POST method
Route::get('/uar/{id}', [UARController::class, 'show'])->name('uar.show');
Route::get('/uar/{id}/edit', [UARController::class, 'edit'])->name('uar.edit');
Route::put('/uar/{id}', [UARController::class, 'update'])->name('uar.update');
Route::delete('/uar/{id}', [UARController::class, 'destroy'])->name('uar.destroy');
Route::get('/uar/{id}/upcoming', [UARController::class, 'showUpcoming'])->name('uar.upcoming.show');
Route::delete('/uar/{id}', [UARController::class, 'destroy'])->name('uar.destroy');
Route::post('/uar/{id}/upload', [UARFileController::class, 'upload'])->name('uar.upload');
Route::get('/uar/{id}/review', [UARUserController::class, 'show'])->name('uar.review');
Route::post('/uar/user/{id}/approve', [UARUserController::class, 'approve'])->name('uar.approve');
Route::post('/uar/user/{id}/reject', [UARUserController::class, 'reject'])->name('uar.reject');
Route::post('/uar/user/{id}/approveall', [UARUserController::class, 'approveAll'])->name('uar.approveAll');

require __DIR__.'/auth.php';
