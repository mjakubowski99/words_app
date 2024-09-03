<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\FlashcardCategory;
use App\Models\User;
use Flashcard\Application\Command\CreateSession;
use Flashcard\Application\Command\CreateSessionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\User\IUser;
use Tests\Base\FlashcardTestCase;

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
        $category_id = $this->createCategoryId(FlashcardCategory::factory()->create());
        $cards_per_session = 5;
        $device = 'Mozilla/Firefox';
        $user = $this->mockery(IUser::class);
        $user->shouldReceive('getId')->andReturn($user_id);
        $command = new CreateSession(
            $user,
            $cards_per_session,
            $device,
            $category_id,
        );

        // WHEN
        $result = $this->command_handler->handle($command);

        // THEN
        $this->assertTrue($result->success());
    }
}
