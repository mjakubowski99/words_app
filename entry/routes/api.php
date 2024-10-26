<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\UserController;
use Flashcard\Infrastructure\Http\Controllers\SessionController;
use Flashcard\Infrastructure\Http\Controllers\FlashcardController;
use Flashcard\Infrastructure\Http\Controllers\FlashcardCategoryController;

Route::get('/test', function () {
    dd(DB::select("
    explain analyze update
        learning_session_flashcards
        set
          rating = CASE
            WHEN id = 7060 THEN 1
          END,
          updated_at = '2024-10-26 12:13:30'
        where
          learning_session_id = '7055'
          and id in (7060)"));
});

Route::post('/user/oauth/login', [UserController::class, 'loginWithProvider'])
    ->name('user.oauth.login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user/me', [UserController::class, 'me'])->name('user.me');

    Route::post('/flashcards', [FlashcardController::class, 'store'])->name('flashcards.store');
    Route::put('/flashcards/{flashcard_id}', [FlashcardController::class, 'update'])->name('flashcards.update');
    Route::delete('/flashcards/{flashcard_id}', [FlashcardController::class, 'delete'])->name('flashcards.delete');

    Route::get('/flashcards/categories/by-user', [FlashcardCategoryController::class, 'index'])
        ->name('flashcards.categories.index');
    Route::get('/flashcards/categories/{category_id}', [FlashcardCategoryController::class, 'get'])
        ->name('flashcards.categories.get');
    Route::post('/flashcards/categories/generate-flashcards', [FlashcardCategoryController::class, 'generateFlashcards'])
        ->name('flashcards.categories.generate-flashcards');
    Route::put('/flashcards/categories/{category_id}/generate-flashcards', [FlashcardCategoryController::class, 'regenerateFlashcards'])
        ->name('flashcards.categories.regenerate-flashcards');

    Route::get('/flashcards/session/{session_id}', [SessionController::class, 'get'])
        ->name('flashcards.session.get');
    Route::post('/flashcards/session', [SessionController::class, 'store'])
        ->name('flashcards.session.store');
    Route::put('/flashcards/session/{session_id}/rate-flashcards', [SessionController::class, 'rate'])
        ->name('flashcards.session.rate');
});
