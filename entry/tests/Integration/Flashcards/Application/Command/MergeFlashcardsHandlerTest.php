<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Shared\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\MergeFlashcardDecksHandler;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = $this->app->make(MergeFlashcardDecksHandler::class);
});
test('handle when user is deck owner success', function () {
    // GIVEN
    $user = $this->createUser();
    $from_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $to_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $from_flashcard = $this->createFlashcard(['flashcard_deck_id' => $to_deck->id]);
    $to_flashcard = $this->createFlashcard(['flashcard_deck_id' => $to_deck->id]);

    // WHEN
    $result = $this->handler->handle(
        $user->toOwner(),
        $from_deck->getId(),
        $to_deck->getId(),
        'New name'
    );

    // THEN
    expect($result)->toBeTrue();
    $this->assertDatabaseHas('flashcards', [
        'id' => $from_flashcard->id,
        'flashcard_deck_id' => $to_deck->id,
    ]);
    $this->assertDatabaseHas('flashcards', [
        'id' => $to_flashcard->id,
        'flashcard_deck_id' => $to_deck->id,
    ]);
    $this->assertDatabaseHas('flashcard_decks', [
        'id' => $to_deck->id,
        'name' => 'New name',
    ]);
    $this->assertDatabaseMissing('flashcard_decks', ['id' => $from_deck->id]);
});
test('handle whendeck has sessions success', function () {
    // GIVEN
    $user = $this->createUser();
    $from_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $to_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $from_learning_session = $this->createLearningSession([
        'flashcard_deck_id' => $from_deck->id,
    ]);

    // WHEN
    $result = $this->handler->handle($user->toOwner(), $from_deck->getId(), $to_deck->getId(), 'New name');

    // THEN
    expect($result)->toBeTrue();
    $this->assertDatabaseHas('learning_sessions', [
        'id' => $from_learning_session->id,
        'flashcard_deck_id' => $to_deck->id,
    ]);
});
test('handle when user is not to deck owner fail', function () {
    // GIVEN
    $user = $this->createUser();
    $from_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $to_deck = $this->createFlashcardDeck();

    // THEN
    $this->expectException(UnauthorizedException::class);

    // WHEN
    $this->handler->handle($user->toOwner(), $from_deck->getId(), $to_deck->getId(), 'New name');
});
test('handle when user is not from deck owner fail', function () {
    // GIVEN
    $user = $this->createUser();
    $from_deck = $this->createFlashcardDeck();
    $to_deck = $this->createFlashcardDeck(['user_id' => $user->id]);

    // THEN
    $this->expectException(UnauthorizedException::class);

    // WHEN
    $this->handler->handle($user->toOwner(), $from_deck->getId(), $to_deck->getId(), 'New name');
});
