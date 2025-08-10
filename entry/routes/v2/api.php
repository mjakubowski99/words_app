<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use User\Infrastructure\Http\Controllers\v2\AuthController;
use User\Infrastructure\Http\Controllers\v2\UserController;
use Exercise\Infrastructure\Http\Controllers\ExerciseController;
use Flashcard\Infrastructure\Http\Controllers\v2\SessionController;
use Flashcard\Infrastructure\Http\Controllers\v2\FlashcardController;
use Flashcard\Infrastructure\Http\Controllers\v2\FlashcardDeckController;

Route::post('/login', [AuthController::class, 'login'])
    ->name('user.login')
    ->middleware('throttle:5,1');

Route::post('/reports', [UserController::class, 'storeReport'])->name('reports.store');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::delete('/user/me', [UserController::class, 'delete'])->name('user.me.delete');

    Route::post('/flashcards', [FlashcardController::class, 'store'])->name('v2.flashcards.store');
    Route::put('/flashcards/{flashcard_id}', [FlashcardController::class, 'update'])->name('v2.flashcards.update');
    Route::delete('/flashcards/bulk-delete', [FlashcardController::class, 'bulkDelete'])
        ->name('v2.flashcards.bulk-delete');

    Route::get('/flashcards/decks/by-user', [FlashcardDeckController::class, 'index'])
        ->name('v2.flashcards.decks.index');
    Route::get('/flashcards/decks/by-admins', [FlashcardDeckController::class, 'indexAdmin'])
        ->name('v2.flashcards.decks.by-admins.index');
    Route::get('/flashcards/by-user', [FlashcardController::class, 'getByUser'])
        ->name('v2.flashcards.get.by-user');
    Route::get('/flashcards/by-user/rating-stats', [FlashcardController::class, 'userRatingStats'])
        ->name('v2.flashcards.get.by-user.rating-stats');
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
    Route::post('/flashcards/decks', [FlashcardDeckController::class, 'store'])
        ->name('v2.flashcards.decks.store');
    Route::put('/flashcards/decks/{flashcard_deck_id}', [FlashcardDeckController::class, 'update'])
        ->name('v2.flashcards.decks.update');
    Route::delete('/flashcards/decks/bulk-delete', [FlashcardDeckController::class, 'bulkDelete'])
        ->name('v2.flashcards.decks.bulk-delete');

    Route::get('/flashcards/session/{session_id}', [SessionController::class, 'get'])
        ->name('v2.flashcards.session.get');
    Route::post('/flashcards/session', [SessionController::class, 'store'])
        ->name('v2.flashcards.session.store');
    Route::put('/flashcards/session/{session_id}/rate-flashcards', [SessionController::class, 'rate'])
        ->name('v2.flashcards.session.rate');

    Route::put('/exercises/unscramble-words/{exercise_entry_id}/answer', [ExerciseController::class, 'answerUnscrambleWordExercise'])
        ->name('v2.exercises.unscramble-words.answer');
    Route::put('/exercises/unscramble-words/{exercise_id}/skip', [ExerciseController::class, 'skipUnscrambleWordExercise'])
        ->name('v2.exercises.unscramble-words.skip');

    Route::put('/exercises/word-match/{exercise_id}/answer', [ExerciseController::class, 'answerWordMatchExercise'])
        ->name('v2.exercises.word-match.answer');
    Route::put('/exercises/word-match/{exercise_id}/skip', [ExerciseController::class, 'skipWordMatchExercise'])
        ->name('v2.exercises.word-match.skip');
});
