<?php

declare(strict_types=1);

use App\Models\Admin;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Flashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardRepository::class);
});
test('get by deck return correct data', function () {
    // GIVEN
    $deck = $this->createFlashcardDeck();
    $expected_flashcard = $this->createFlashcard(['flashcard_deck_id' => $deck->id]);

    // WHEN
    $flashcards = $this->repository->getByDeck($deck->getId());

    // THEN
    expect($flashcards)->toHaveCount(1);
    expect($flashcards[0])->toBeInstanceOf(Flashcard::class);
    $flashcard = $flashcards[0];
    expect($flashcard->getId()->getValue())->toBe($expected_flashcard->id);
    expect($flashcard->getFrontWord())->toBe($expected_flashcard->front_word);
    expect($flashcard->getBackWord())->toBe($expected_flashcard->back_word);
    expect($flashcard->getBackLang()->getValue())->toBe($expected_flashcard->back_lang);
    expect($flashcard->getFrontLang()->getValue())->toBe($expected_flashcard->front_lang);
    expect($flashcard->getBackContext())->toBe($expected_flashcard->back_context);
    expect($flashcard->getFrontContext())->toBe($expected_flashcard->front_context);
    expect($flashcard->getEmoji()->toUnicode())->toBe($expected_flashcard->emoji);
});
test('get by deck admin is owner', function () {
    // GIVEN
    $deck = $this->createFlashcardDeck();
    $admin = Admin::factory()->create();
    $expected_flashcard = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'admin_id' => $admin->id,
        'user_id' => null,
    ]);

    // WHEN
    $flashcards = $this->repository->getByDeck($deck->getId());

    // THEN
    expect($flashcards)->toHaveCount(1);
    expect($flashcards[0])->toBeInstanceOf(Flashcard::class);
    $flashcard = $flashcards[0];
    expect($flashcard->getOwner()->isAdmin())->toBeTrue();
    expect($flashcard->getOwner()->getId()->getValue())->toBe($expected_flashcard->admin_id);
});
test('get by deck return only flashcards for given deck', function () {
    // GIVEN
    $deck = $this->createFlashcardDeck();
    $other_flashcard = $this->createFlashcard();
    $flashcard = $this->createFlashcard(['flashcard_deck_id' => $deck->id]);

    // WHEN
    $flashcards = $this->repository->getByDeck($deck->getId());

    // THEN
    expect($flashcards)->toHaveCount(1);
    expect($flashcards[0])->toBeInstanceOf(Flashcard::class);
    expect($flashcards[0]->getId()->getValue())->toBe($flashcard->getId()->getValue());
});
