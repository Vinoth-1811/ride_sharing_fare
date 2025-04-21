<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;

Route::post('/api/calculate-fare', [TripController::class, 'calculateFare']);
Route::post('/api/trip', [TripController::class, 'storeTrip']);
