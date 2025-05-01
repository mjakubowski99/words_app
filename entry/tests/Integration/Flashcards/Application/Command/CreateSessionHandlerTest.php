<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\User;
use App\Models\FlashcardDeck;
use Shared\Enum\SessionType;
use Tests\Base\FlashcardTestCase;
use Shared\Enum\LearningSessionType;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Application\Command\CreateSession;
use Flashcard\Application\Command\CreateSessionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateSessionHandlerTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private CreateSessionHandler $command_handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command_handler = $this->app->make(CreateSessionHandler::class);
    }

    /**
     * @test
     */
    public function createSessionHandler_ShouldCreateSession(): void
    {
        // GIVEN
        $user_id = User::factory()->create()->getId();
        $deck_id = $this->createDeckId(FlashcardDeck::factory()->create([
            'user_id' => $user_id->getValue(),
        ]));
        $cards_per_session = 5;
        $device = 'Mozilla/Firefox';
        $command = new CreateSession(
            $user_id,
            $cards_per_session,
            $device,
            $deck_id,
            SessionType::FLASHCARD,
        );

        // WHEN
        $result = $this->command_handler->handle($command);

        // THEN
        $this->assertTrue($result->success());
    }

    /**
     * @test
     */
    public function createSessionHandler_UserIsNotDeckOwner_fail(): void
    {
        // GIVEN
        $user_id = User::factory()->create()->getId();
        $deck_id = $this->createDeckId(FlashcardDeck::factory()->create());
        $cards_per_session = 5;
        $device = 'Mozilla/Firefox';
        $command = new CreateSession(
            $user_id,
            $cards_per_session,
            $device,
            $deck_id,
            SessionType::FLASHCARD,
        );

        // THEN
        $this->expectException(ForbiddenException::class);

        // WHEN
        $result = $this->command_handler->handle($command);
    }
}
