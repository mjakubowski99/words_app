<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\UserController;
use Flashcard\Infrastructure\Http\Controllers\v1\SessionController;
use Flashcard\Infrastructure\Http\Controllers\v1\FlashcardController;
use Flashcard\Infrastructure\Http\Controllers\v1\FlashcardDeckController;

Route::post('/user/oauth/login', [UserController::class, 'loginWithProvider'])
    ->name('user.oauth.login');
Route::post('/tickets', [UserController::class, 'storeTicket'])->name('tickets.store');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user/me', [UserController::class, 'me'])->name('user.me');
    Route::delete('/user/me', [UserController::class, 'delete'])->name('user.me.delete');

    Route::post('/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::put('/flashcards/{flashcard_id}', [FlashcardController::class, 'update'])->name('flashcards.update');
    Route::delete('/flashcards/{flashcard_id}', [FlashcardController::class, 'delete'])->name('flashcards.delete');

    Route::get('/flashcards/categories/by-user', [FlashcardDeckController::class, 'index'])
        ->name('flashcards.categories.index');
    Route::get('/flashcards/categories/{category_id}', [FlashcardDeckController::class, 'get'])
        ->name('flashcards.categories.get');
    Route::post('/flashcards/categories/generate-flashcards', [FlashcardDeckController::class, 'generateFlashcards'])
        ->name('flashcards.categories.generate-flashcards');
    Route::post('/flashcards/categories/{from_category_id}/merge-flashcards/{to_category_id}', [FlashcardDeckController::class, 'merge'])
        ->name('flashcards.categories.merge-flashcards');
    Route::put('/flashcards/categories/{category_id}/generate-flashcards', [FlashcardDeckController::class, 'regenerateFlashcards'])
        ->name('flashcards.categories.regenerate-flashcards');

    Route::get('/flashcards/session/{session_id}', [SessionController::class, 'get'])
        ->name('flashcards.session.get');
    Route::post('/flashcards/session', [SessionController::class, 'store'])
        ->name('flashcards.session.store');
    Route::put('/flashcards/session/{session_id}/rate-flashcards', [SessionController::class, 'rate'])
        ->name('flashcards.session.rate');
});
