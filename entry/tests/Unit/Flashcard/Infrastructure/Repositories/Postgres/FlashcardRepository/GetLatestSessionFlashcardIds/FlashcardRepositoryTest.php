<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardRepository::class);
});
test('get latest session flashcard ids should return limited latest session flashcard ids', function () {
    // GIVEN
    $session = $this->createLearningSession();
    $session_flashcards = [
        $this->createLearningSessionFlashcard([
            'learning_session_id' => $session->id,
            'created_at' => now()->subMinute(),
        ]),
        $this->createLearningSessionFlashcard([
            'learning_session_id' => $session->id,
            'created_at' => now()->subSecond(),
        ]),
        $this->createLearningSessionFlashcard([
            'learning_session_id' => $session->id,
            'created_at' => now()->subSeconds(2),
        ]),
    ];

    // WHEN
    $results = $this->repository->getLatestSessionFlashcardIds($session->getId(), 2);

    // THEN
    expect($results)->toHaveCount(2);
    expect($results[0])->toBeInstanceOf(FlashcardId::class);
    expect($results[1])->toBeInstanceOf(FlashcardId::class);
    expect($results[0]->getValue())->toBe($session_flashcards[1]->flashcard->id);
    expect($results[1]->getValue())->toBe($session_flashcards[2]->flashcard->id);
});
