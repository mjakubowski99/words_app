<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\User;
use App\Models\Exercise;
use App\Models\Flashcard;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use App\Models\LearningSession;
use Tests\Base\FlashcardTestCase;
use App\Models\LearningSessionFlashcard;
use Flashcard\Application\Command\AddSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\AddSessionFlashcardsHandler;

class AddSessionFlashcardsTest extends FlashcardTestCase
{
    use DatabaseTransactions;
    private AddSessionFlashcardsHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(AddSessionFlashcardsHandler::class);
    }

    /**
     * @test
     */
    public function handle_ShouldAddNewFlashcardsToSession(): void
    {
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
        $command = new AddSessionFlashcards($session->getId(), $user->getId(), 2);

        // WHEN
        $this->handler->handle($command);

        // THEN
        $this->assertDatabaseCount('learning_session_flashcards', 2);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);
    }

    /**
     * @test
     */
    public function handle_WhenUnscrambleWordsSession_ShouldAddNewFlashcardsToSession(): void
    {
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
        $command = new AddSessionFlashcards($session->getId(), $user->getId(), 1);

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
    }
}
