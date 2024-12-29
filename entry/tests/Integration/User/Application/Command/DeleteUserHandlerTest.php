<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application\Command;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use User\Application\Command\DeleteUserHandler;

class DeleteUserHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private DeleteUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(DeleteUserHandler::class);
    }

    public function test__handle_ShouldDeleteUserWithAllData(): void
    {
        // GIVEN
        $user = $this->createUser();
        $deck = FlashcardDeck::factory()->create(['user_id' => $user->id]);
        $flashcard = Flashcard::factory()->create(['user_id' => $user->id]);
        $other_flashcard = Flashcard::factory()->create(['user_id' => $user->id]);
        $learning_session = LearningSession::factory()->create(['user_id' => $user->id]);
        $learning_session_flashcard = LearningSessionFlashcard::factory()->create([
            'flashcard_id' => $flashcard->id,
        ]);
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $result = $this->handler->delete($user->getId());

        // THEN
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        $this->assertDatabaseMissing('flashcard_decks', [
            'id' => $deck->id,
        ]);
        $this->assertDatabaseMissing('flashcards', [
            'id' => $flashcard->id,
        ]);
        $this->assertDatabaseMissing('flashcards', [
            'id' => $other_flashcard->id,
        ]);
        $this->assertDatabaseMissing('learning_sessions', [
            'id' => $learning_session->id,
        ]);
        $this->assertDatabaseMissing('learning_session_flashcards', [
            'id' => $learning_session_flashcard->id,
        ]);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => null,
        ]);
    }
}
