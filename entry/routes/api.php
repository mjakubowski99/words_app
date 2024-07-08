<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::get('/user/me', [UserController::class, 'me'])->name('user.me')->middleware('auth:firebase');
