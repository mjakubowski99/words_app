<?php

declare(strict_types=1);

use App\Models\ExerciseEntry;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\GetSessionScoreHandler;

uses(DatabaseTransactions::class);
uses(FlashcardTestCase::class);

beforeEach(function () {
    $this->command_handler = $this->app->make(GetSessionScoreHandler::class);
});

test('handle should return score for session', function () {
    // GIVEN
    $session = $this->createLearningSession();
    $entry = ExerciseEntry::factory()->create(['score' => 80.0]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $session->id,
        'rating' => null,
        'exercise_entry_id' => $entry->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $session->id,
        'rating' => null,
        'exercise_entry_id' => null,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $session->id,
        'rating' => Rating::VERY_GOOD,
        'exercise_entry_id' => null,
    ]);

    // WHEN
    $score = $this->command_handler->handle($session->getId());

    // THEN
    expect($score)->toBe((80.0 + 100.0) / 3);
});
