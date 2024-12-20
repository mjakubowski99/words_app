<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Flashcard\Infrastructure\Http\Controllers\v2\SessionController;
use Flashcard\Infrastructure\Http\Controllers\v2\FlashcardController;
use Flashcard\Infrastructure\Http\Controllers\v2\FlashcardDeckController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/flashcards', [FlashcardController::class, 'store'])->name('v2.flashcards.store');
    Route::put('/flashcards/{flashcard_id}', [FlashcardController::class, 'update'])->name('v2.flashcards.update');

    Route::get('/flashcards/decks/by-user', [FlashcardDeckController::class, 'index'])
        ->name('v2.flashcards.decks.index');
    Route::get('/flashcards/decks/{flashcard_deck_id}', [FlashcardDeckController::class, 'get'])
        ->name('v2.flashcards.decks.get');
    Route::get('/flashcards/decks/{flashcard_deck_id}/rating-stats', [FlashcardDeckController::class, 'deckRatingStats'])
        ->name('v2.flashcards.decks.rating-stats');
    Route::post('/flashcards/decks/generate-flashcards', [FlashcardDeckController::class, 'generateFlashcards'])
        ->name('v2.flashcards.decks.generate-flashcards');
    Route::post('/flashcards/decks/{from_deck_id}/merge-flashcards/{to_deck_id}', [FlashcardDeckController::class, 'merge'])
        ->name('v2.flashcards.decks.merge-flashcards');
    Route::put('/flashcards/decks/{flashcard_deck_id}/generate-flashcards', [FlashcardDeckController::class, 'regenerateFlashcards'])
        ->name('v2.flashcards.decks.regenerate-flashcards');

    Route::get('/flashcards/session/{session_id}', [SessionController::class, 'get'])
        ->name('v2.flashcards.session.get');
    Route::post('/flashcards/session', [SessionController::class, 'store'])
        ->name('v2.flashcards.session.store');
    Route::put('/flashcards/session/{session_id}/rate-flashcards', [SessionController::class, 'rate'])
        ->name('v2.flashcards.session.rate');
});
