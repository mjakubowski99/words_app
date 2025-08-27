<?php

declare(strict_types=1);
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\SessionReadRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(SessionReadRepository::class);
});
test('find should find correct object', function () {
    // GIVEN
    $other_session = LearningSession::factory()->create();
    $session = LearningSession::factory()->create([
        'status' => SessionStatus::FINISHED,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getId()->getValue())->toBe($session->id);
    expect($result->getCardsPerSession())->toBe($session->cards_per_session);
    expect($result->getProgress())->toBe(0);
    expect($result->isFinished())->toBeTrue();
});
test('find when session has rated flashcards should return correct progress', function () {
    // GIVEN
    $session = LearningSession::factory()->create([
        'cards_per_session' => 5,
    ]);
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getId()->getValue())->toBe($session->id);
    expect($result->getProgress())->toBe(1);
});
test('find when session has not rated flashcards should return correct progress', function () {
    // GIVEN
    $session = LearningSession::factory()->create([
        'cards_per_session' => 5,
    ]);
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getId()->getValue())->toBe($session->id);
    expect($result->getProgress())->toBe(0);
});
