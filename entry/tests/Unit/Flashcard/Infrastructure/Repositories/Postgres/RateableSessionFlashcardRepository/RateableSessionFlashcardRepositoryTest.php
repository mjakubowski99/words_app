<?php

declare(strict_types=1);
use App\Models\User;
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\RateableSessionFlashcardsRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(RateableSessionFlashcardsRepository::class);
});
test('find should find correct object', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    LearningSessionFlashcard::factory()->create([
        'rating' => null,
    ]);
    $flashcard = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getRateableSessionFlashcards()[0]->getId()->getValue())->toBe($flashcard->id);
    expect($result->getRateableSessionFlashcards()[0]->rated())->toBeFalse();
});
test('find should find only unrated flashcards', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD,
    ]);
    $flashcard = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getSessionUserId()->getValue())->toBe($session->user_id);
    expect(count($result->getRateableSessionFlashcards()))->toBe(1);
    expect($result->getRateableSessionFlashcards()[0]->getId()->getValue())->toBe($flashcard->id);
});
test('save should persist flashcard ratings', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    $not_to_rate = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);
    $learning_session_flashcards = LearningSessionFlashcard::factory(2)->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);
    $user = User::factory()->create();

    $flashcards = new RateableSessionFlashcards(
        $session->getId(),
        $user->getId(),
        null,
        SessionStatus::IN_PROGRESS,
        9,
        10,
        [
            new RateableSessionFlashcard(
                $learning_session_flashcards[0]->getId(),
                $learning_session_flashcards[0]->flashcard->getId(),
            ),
            new RateableSessionFlashcard(
                $learning_session_flashcards[1]->getId(),
                $learning_session_flashcards[1]->flashcard->getId(),
            ),
        ]
    );
    $flashcards->rate($learning_session_flashcards[0]->getId(), Rating::WEAK);
    $flashcards->rate($learning_session_flashcards[1]->getId(), Rating::GOOD);

    // WHEN
    $this->repository->save($flashcards);

    // THEN
    $this->assertDatabaseHas('learning_sessions', [
        'id' => $session->id,
        'status' => SessionStatus::FINISHED->value,
    ]);
    $this->assertDatabaseHas('learning_session_flashcards', [
        'id' => $learning_session_flashcards[0]->id,
        'rating' => Rating::WEAK,
    ]);
    $this->assertDatabaseHas('learning_session_flashcards', [
        'id' => $learning_session_flashcards[1]->id,
        'rating' => Rating::GOOD,
    ]);
    $this->assertDatabaseHas('learning_session_flashcards', [
        'id' => $not_to_rate->id,
        'rating' => null,
    ]);
});
