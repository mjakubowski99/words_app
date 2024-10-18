<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\UserController;
use Flashcard\Infrastructure\Http\Controllers\SessionController;
use Flashcard\Infrastructure\Http\Controllers\FlashcardCategoryController;

Route::get('/test', function () {
    return 1;
});

Route::post('/user/oauth/login', [UserController::class, 'loginWithProvider'])
    ->name('user.oauth.login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user/me', [UserController::class, 'me'])->name('user.me');

    Route::get('/flashcards/categories/by-user', [FlashcardCategoryController::class, 'index'])
        ->name('flashcards.categories.index');
    Route::get('/flashcards/categories/{category_id}', [FlashcardCategoryController::class, 'get'])
        ->name('flashcards.categories.get');
    Route::post('/flashcards/categories/generate-flashcards', [FlashcardCategoryController::class, 'generateFlashcards'])
        ->name('flashcards.categories.generate-flashcards');

    Route::get('/flashcards/session/{session_id}', [SessionController::class, 'get'])
        ->name('flashcards.session.get');
    Route::post('/flashcards/session', [SessionController::class, 'store'])
        ->name('flashcards.session.store');
    Route::put('/flashcards/session/{session_id}/rate-flashcards', [SessionController::class, 'rate'])
        ->name('flashcards.session.rate');
});
