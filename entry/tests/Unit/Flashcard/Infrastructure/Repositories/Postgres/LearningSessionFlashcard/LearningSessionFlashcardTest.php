<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\LearningSessionFlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(LearningSessionFlashcardRepository::class);
});

test('getFlashcardRatings return all ratings for session', function () {
    // GIVEN
    $session = $this->createLearningSession();
    $flashcards = [
        $this->createLearningSessionFlashcard(['exercise_entry_id' => null]),
        $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'rating' => Rating::VERY_GOOD, 'exercise_entry_id' => 1]),
        $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'rating' => null, 'exercise_entry_id' => null]),
        $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'rating' => Rating::GOOD, 'exercise_entry_id' => null]),
    ];

    // WHEN
    $ratings = $this->repository->getFlashcardRatings($session->getId());

    // THEN
    expect($ratings)
        ->toHaveCount(2)
        ->and($ratings[0]->getValue())->toBe(GeneralRatingType::NEW)
        ->and($ratings[1]->getValue())->toBe(GeneralRatingType::GOOD);
});

test('getExerciseEntries return all exercise entry ids for session', function () {
    // GIVEN
    $session = $this->createLearningSession();
    $flashcards = [
        $this->createLearningSessionFlashcard(['exercise_entry_id' => null]),
        $this->createLearningSessionFlashcard(['exercise_entry_id' => 12]),
        $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'rating' => Rating::VERY_GOOD, 'exercise_entry_id' => 1]),
        $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'rating' => Rating::VERY_GOOD, 'exercise_entry_id' => 2]),
    ];

    // WHEN
    $entry_ids = $this->repository->getExerciseEntryIds($session->getId());

    // THEN
    expect($entry_ids)
        ->toHaveCount(2)
        ->and($entry_ids[0])->toBe(1)
        ->and($entry_ids[1])->toBe(2);
});
