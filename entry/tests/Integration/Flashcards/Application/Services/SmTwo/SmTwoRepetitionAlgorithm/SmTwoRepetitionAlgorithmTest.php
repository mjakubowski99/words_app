<?php

declare(strict_types=1);
use App\Models\SmTwoFlashcard;
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Services\SmTwo\SmTwoRepetitionAlgorithm;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->algorithm = $this->app->make(SmTwoRepetitionAlgorithm::class);
});
test('handle when no sm two flashcards should create new sm two flashcards', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    $flashcards = LearningSessionFlashcard::factory(2)->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);
    $rateable_flashcards = new RateableSessionFlashcards(
        $session->getId(),
        $session->user->getId(),
        null,
        SessionStatus::IN_PROGRESS,
        0,
        2,
        [
            new RateableSessionFlashcard($flashcards[0]->getId(), $flashcards[0]->flashcard->getId()),
            new RateableSessionFlashcard($flashcards[1]->getId(), $flashcards[1]->flashcard->getId()),
        ]
    );
    $rateable_flashcards->rate($flashcards[0]->getId(), Rating::WEAK);
    $rateable_flashcards->rate($flashcards[1]->getId(), Rating::GOOD);

    // WHEN
    $this->algorithm->handle($rateable_flashcards);

    // THEN
    $this->assertDatabaseHas('sm_two_flashcards', [
        'flashcard_id' => $flashcards[0]->flashcard->id,
        'user_id' => $session->user_id,
    ]);
    $this->assertDatabaseHas('sm_two_flashcards', [
        'flashcard_id' => $flashcards[1]->flashcard->id,
        'user_id' => $session->user_id,
    ]);
});
test('handle when sm two flashcard exists should update existing flashcards', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    $flashcard = LearningSessionFlashcard::factory()->create(['learning_session_id' => $session->id]);
    $sm_two_flashcard = SmTwoFlashcard::factory()->create([
        'flashcard_id' => $flashcard->flashcard->id,
        'user_id' => $session->user_id,
    ]);
    $rateable_flashcards = new RateableSessionFlashcards(
        $session->getId(),
        $session->user->getId(),
        null,
        SessionStatus::IN_PROGRESS,
        0,
        2,
        [new RateableSessionFlashcard($flashcard->getId(), $flashcard->flashcard->getId())]
    );
    $rateable_flashcards->rate($flashcard->getId(), Rating::WEAK);

    // WHEN
    $this->algorithm->handle($rateable_flashcards);

    // THEN
    $this->assertDatabaseCount('sm_two_flashcards', 1);
    $this->assertDatabaseHas('sm_two_flashcards', [
        'flashcard_id' => $sm_two_flashcard->flashcard_id,
        'user_id' => $sm_two_flashcard->user_id,
    ]);
});
