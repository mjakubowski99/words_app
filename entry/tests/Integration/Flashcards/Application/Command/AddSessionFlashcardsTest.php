<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Story;
use App\Models\Exercise;
use App\Models\Flashcard;
use Shared\Enum\Language;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use App\Models\StoryFlashcard;
use App\Models\LearningSession;
use Tests\Base\FlashcardTestCase;
use App\Models\LearningSessionFlashcard;
use Flashcard\Application\Command\AddSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\AddSessionFlashcardsHandler;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = $this->app->make(AddSessionFlashcardsHandler::class);
});
test('handle should add new flashcards to session', function () {
    // GIVEN
    LearningSessionFlashcard::query()->forceDelete();
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcards = Flashcard::factory(3)->create([
        'flashcard_deck_id' => $deck->id,
        'user_id' => $user->id,
    ]);
    $session = LearningSession::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'user_id' => $user->id,
        'type' => SessionType::FLASHCARD->value,
    ]);
    $command = new AddSessionFlashcards($session->getId(), $user->getId(), Language::PL, Language::EN, 2);

    // WHEN
    $this->handler->handle($command);

    // THEN
    $this->assertDatabaseCount('learning_session_flashcards', 2);
    $this->assertDatabaseHas('learning_session_flashcards', [
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);
});
test('handle when unscramble words session should add new flashcards to session', function () {
    // GIVEN
    Exercise::query()->forceDelete();
    LearningSessionFlashcard::query()->forceDelete();
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcards = Flashcard::factory(3)->create([
        'flashcard_deck_id' => $deck->id,
        'user_id' => $user->id,
    ]);
    $session = LearningSession::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'user_id' => $user->id,
        'type' => SessionType::UNSCRAMBLE_WORDS->value,
    ]);
    $command = new AddSessionFlashcards($session->getId(), $user->getId(), Language::PL, Language::EN, 1);

    // WHEN
    $this->handler->handle($command);

    // THEN
    $this->assertDatabaseHas('learning_session_flashcards', [
        'learning_session_id' => $session->id,
        'flashcard_id' => $flashcards[0]->id,
        'rating' => null,
    ]);
    $this->assertDatabaseCount('exercise_entries', 1);
    $this->assertDatabaseCount('exercises', 1);
    $this->assertDatabaseCount('unscramble_word_exercises', 1);
});

test('handle when word match exercise session should add new flashcards to session', function () {
    // GIVEN
    Exercise::query()->forceDelete();
    LearningSessionFlashcard::query()->forceDelete();
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $story = Story::factory()->create();
    $flashcards = Flashcard::factory(3)->create([
        'flashcard_deck_id' => $deck->id,
        'user_id' => $user->id,
    ]);
    foreach ($flashcards as $flashcard) {
        StoryFlashcard::create([
            'story_id' => $story->id,
            'flashcard_id' => $flashcard->id,
        ]);
    }
    $session = LearningSession::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'user_id' => $user->id,
        'type' => SessionType::WORD_MATCH->value,
    ]);
    $command = new AddSessionFlashcards($session->getId(), $user->getId(), Language::PL, Language::EN, 1);

    // WHEN
    $this->handler->handle($command);

    // THEN
    $this->assertDatabaseCount('learning_session_flashcards', 3);
    foreach ($flashcards as $flashcard) {
        $this->assertDatabaseHas('learning_session_flashcards', [
            'learning_session_id' => $session->id,
            'flashcard_id' => $flashcard->id,
            'rating' => null,
            'is_additional' => false,
        ]);
    }
});
