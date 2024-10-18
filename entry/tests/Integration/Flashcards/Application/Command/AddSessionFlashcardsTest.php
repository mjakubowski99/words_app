<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\User;
use App\Models\Flashcard;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
use Tests\Base\FlashcardTestCase;
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
        $user = User::factory()->create();
        $category = FlashcardCategory::factory()->create();
        $flashcards = Flashcard::factory(3)->create([
            'flashcard_category_id' => $category->id,
            'user_id' => $user->id,
        ]);
        $session = LearningSession::factory()->create([
            'flashcard_category_id' => $category->id,
            'user_id' => $user->id,
        ]);
        $command = new AddSessionFlashcards($session->getId(), 2);

        // WHEN
        $this->handler->handle($command);

        // THEN
        $this->assertDatabaseCount('learning_session_flashcards', 2);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);
    }
}
