<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application\Command;

use App\Models\Flashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Enum\ReportableType;
use Shared\Enum\TicketType;
use Tests\TestCase;
use User\Application\Command\CreateTicket;
use User\Application\Command\CreateTicketHandler;
use User\Infrastructure\Entities\Ticket;

class CreateTicketHandlerTest extends TestCase
{
    use DatabaseTransactions;

    private CreateTicketHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(CreateTicketHandler::class);
    }

    public function test__handle_WhenDeleteFlashcardReport_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $command = new CreateTicket(
            null,
            'email@email.com',
            TicketType::DELETE_ACCOUNT,
            'description'
        );

        // WHEN
        $this->handler->handle($command);

        // THEN
        $ticket = Ticket::query()
            ->where([
                'email' => 'email@email.com',
                'user_id' => null,
                'type' => TicketType::DELETE_ACCOUNT,
            ])->first();

        $this->assertNotNull($ticket);
        $this->assertNull($ticket->context);
    }

    public function test__handle_WhenFlashcardReport_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $flashcard = Flashcard::factory()->create();

        $command = new CreateTicket(
            $user->getId(),
            'email@email.com',
            TicketType::INAPPROPRIATE_CONTENT,
            'description',
            (string) $flashcard->id,
            ReportableType::FLASHCARD
        );

        // WHEN
        $this->handler->handle($command);

        // THEN
        $ticket = Ticket::query()
            ->where([
                'email' => 'email@email.com',
                'user_id' => $user->id,
                'type' => TicketType::INAPPROPRIATE_CONTENT,
            ])->first();

        $this->assertNotNull($ticket);
        $this->assertSame(
            json_encode([
                'reportable_id' => (string) $flashcard->id,
                'reportable_type' => ReportableType::FLASHCARD,
            ]),
            json_encode($ticket->context)
        );
    }
}
