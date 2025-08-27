<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Flashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardRepository::class);
});
test('get random flashcards return user flashcards', function () {
    // GIVEN
    $owner = $this->createUser();
    $other_flashcard = $this->createFlashcard();
    $flashcard = $this->createFlashcard(['user_id' => $owner->id]);

    // WHEN
    $flashcards = $this->repository->getRandomFlashcards($owner->getId(), 5, []);

    // THEN
    expect($flashcards)->toHaveCount(1);
    expect($flashcards[0])->toBeInstanceOf(Flashcard::class);
    expect($flashcards[0]->getId()->getValue())->toBe($flashcard->getId()->getValue());
});
test('get random flashcards limit works', function () {
    // GIVEN
    $owner = $this->createUser();
    $other_flashcard = $this->createFlashcard();
    $this->createFlashcard(['user_id' => $owner->id]);
    $this->createFlashcard(['user_id' => $owner->id]);

    // WHEN
    $flashcards = $this->repository->getRandomFlashcards($owner->getId(), 1, []);

    // THEN
    expect($flashcards)->toHaveCount(1);
    expect($flashcards[0])->toBeInstanceOf(Flashcard::class);
});
test('get random flashcards exclude flashcard ids', function () {
    // GIVEN
    $owner = $this->createUser();
    $flashcards = [
        $this->createFlashcard(['user_id' => $owner->id]),
        $this->createFlashcard(['user_id' => $owner->id]),
        $this->createFlashcard(['user_id' => $owner->id]),
    ];
    $expected_flashcard = $flashcards[1];
    $flashcards_to_exclude = [$flashcards[0]->id, $flashcards[2]->id];

    // WHEN
    $flashcards = $this->repository->getRandomFlashcards($owner->getId(), 100, $flashcards_to_exclude);

    // THEN
    expect($flashcards)->toHaveCount(1);
    expect($flashcards[0])->toBeInstanceOf(Flashcard::class);
    expect($flashcards[0]->getId()->getValue())->toBe($expected_flashcard->id);
});
