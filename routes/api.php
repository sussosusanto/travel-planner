<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TravelController;

// User Authentication Routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Travel Management Routes
Route::get('/travels', [TravelController::class, 'index'])->middleware('auth:sanctum');
Route::get('/travels/{travel}', [TravelController::class, 'show'])->middleware('auth:sanctum');
Route::post('/travels', [TravelController::class, 'store'])->middleware('auth:sanctum');
Route::put('/travels/{travel}', [TravelController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/travels/{travel}', [TravelController::class, 'destroy'])->middleware('auth:sanctum');
