<?php

declare(strict_types=1);
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Flashcard\Application\Query\GetDeckDetails;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->query = $this->app->make(GetDeckDetails::class);
});
test('handle should return category', function () {
    // GIVEN
    $deck = FlashcardDeck::factory()->create();
    $flashcard = Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'language_level' => LanguageLevel::C1->value,
    ]);

    // WHEN
    $result = $this->query->get($deck->getUserId(), $deck->getId(), null, 1, 15);

    // THEN
    expect($result->getId()->getValue())->toBe($deck->id);
    expect($result->getName())->toBe($deck->name);
    expect(count($result->getFlashcards()))->toBe(1);
    expect($result->getFlashcards()[0]->getLanguageLevel()->value)->toBe($flashcard->language_level);
});
