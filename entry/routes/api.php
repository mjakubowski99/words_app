<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\UserController;
use Flashcard\Infrastructure\Http\Controllers\SessionController;
use Flashcard\Infrastructure\Http\Controllers\FlashcardCategoryController;

Route::group(['middleware' => 'auth:firebase'], function () {
    Route::post('/user/firebase-init', [UserController::class, 'initFirebaseUser'])->name('user.firebase-init');
    Route::get('/user/me', [UserController::class, 'me'])->name('user.me');

    Route::post('/flashcards/generate-by-category', [FlashcardCategoryController::class, 'generateFlashcards'])
        ->name('flashcards.generate-by-category');
    Route::get('/flashcards/categories/by-user', [FlashcardCategoryController::class, 'index'])
        ->name('flashcards.categories.index');

    Route::get('/flashcards/session/{session_id}', [SessionController::class, 'get']);
    Route::post('/flashcards/session', [SessionController::class, 'store'])->name('flashcards.session.store');
    Route::put('/flashcards/session/{session_id}/rate-flashcards', [SessionController::class, 'rate'])
        ->name('flashcards.session.rate');
});
