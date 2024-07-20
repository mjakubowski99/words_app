<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\UserController;

Route::get('/user/me', [UserController::class, 'me'])->name('user.me')->middleware('auth:firebase');
