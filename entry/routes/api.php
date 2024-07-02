<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::post('/login', [AuthController::class, 'loginUser'])->name('auth.login');
Route::post('/register', [AuthController::class, 'registerUser'])->name('auth.register');

Route::get('/test', [AuthController::class, 'test']);
