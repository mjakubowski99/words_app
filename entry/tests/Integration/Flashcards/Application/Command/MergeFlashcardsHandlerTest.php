<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use Tests\Base\FlashcardTestCase;
use Shared\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\MergeFlashcardDecksHandler;

class MergeFlashcardsHandlerTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private MergeFlashcardDecksHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(MergeFlashcardDecksHandler::class);
    }

    /**
     * @test
     */
    public function handle_WhenUserIsDeckOwner_success(): void
    {
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
        $this->assertTrue($result);
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
    }

    /**
     * @test
     */
    public function handle_WhendeckHasSessions_success(): void
    {
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
        $this->assertTrue($result);
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $from_learning_session->id,
            'flashcard_deck_id' => $to_deck->id,
        ]);
    }

    /**
     * @test
     */
    public function handle_WhenUserIsNotToDeckOwner_fail(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
        $to_deck = $this->createFlashcardDeck();

        // THEN
        $this->expectException(UnauthorizedException::class);

        // WHEN
        $this->handler->handle($user->toOwner(), $from_deck->getId(), $to_deck->getId(), 'New name');
    }

    /**
     * @test
     */
    public function handle_WhenUserIsNotFromDeckOwner_fail(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_deck = $this->createFlashcardDeck();
        $to_deck = $this->createFlashcardDeck(['user_id' => $user->id]);

        // THEN
        $this->expectException(UnauthorizedException::class);

        // WHEN
        $this->handler->handle($user->toOwner(), $from_deck->getId(), $to_deck->getId(), 'New name');
    }
}
