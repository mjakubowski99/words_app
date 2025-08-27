<?php

declare(strict_types=1);
use App\Models\User;
use App\Models\SmTwoFlashcard;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Application\Command\RateFlashcards;
use Flashcard\Application\Command\FlashcardRating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\RateFlashcardsHandler;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->command_handler = $this->app->make(RateFlashcardsHandler::class);
});
test('handle should save flashcard ratings', function () {
    // GIVEN
    $user = User::factory()->create();
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
    ]);
    $session_flashcards = [
        LearningSessionFlashcard::factory()->create(['learning_session_id' => $session->id, 'rating' => null]),
        LearningSessionFlashcard::factory()->create(['learning_session_id' => $session->id, 'rating' => null]),
    ];
    SmTwoFlashcard::factory()->create([
        'user_id' => $user->id,
        'flashcard_id' => $session_flashcards[0]->flashcard_id,
        'repetition_interval' => 1,
    ]);
    $command = new RateFlashcards(
        $user->getId(),
        $session->getId(),
        [
            new FlashcardRating($session_flashcards[0]->getId(), Rating::GOOD),
            new FlashcardRating($session_flashcards[1]->getId(), Rating::VERY_GOOD),
        ]
    );

    // WHEN
    $this->command_handler->handle($command);

    // THEN
    $this->assertDatabaseHas('learning_session_flashcards', [
        'id' => $session_flashcards[0]->id,
        'rating' => Rating::GOOD,
    ]);
    $this->assertDatabaseHas('learning_session_flashcards', [
        'id' => $session_flashcards[1]->id,
        'rating' => Rating::VERY_GOOD,
    ]);
});
