<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public platform homepage (English + Arabic)
Route::view('/', 'home')->name('home');
Route::view('/ar', 'home')->name('home.ar');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
