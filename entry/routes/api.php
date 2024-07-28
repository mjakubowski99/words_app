<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\UserController;

Route::group(['middleware' => 'auth:firebase'], function () {
    Route::post('/user/firebase-init', [UserController::class, 'initFirebaseUser'])->name('user.firebase-init');
    Route::get('/user/me', [UserController::class, 'me'])->name('user.me');
});
