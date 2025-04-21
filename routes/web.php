<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\TripController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/trip', fn() => view('trip'));
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

// Route::post('/api/calculate-fare', [TripController::class, 'calculateFare']);
// Route::post('/api/trip', [TripController::class, 'storeTrip']);
Route::get('/api/users', [TripController::class, 'getUsers']);
Route::get('/api/trips', [TripController::class, 'getTrips']);


Route::middleware('auth')->group(function () {
    Route::get('/trip', fn() => view('trip'));
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Fetch all trips (Read)
    // Route::get('/api/trips', [TripController::class, 'getTrips']);

    // Store trip (Create)
    Route::post('/api/trip', [TripController::class, 'storeTrip']);

    // Calculate fare (POST request)
    Route::post('/api/calculate-fare', [TripController::class, 'calculateFare']);

});



require __DIR__.'/auth.php';
