<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application\Command;

use Tests\TestCase;
use App\Models\Flashcard;
use Shared\Enum\ReportType;
use Shared\Enum\ReportableType;
use User\Infrastructure\Entities\Report;
use User\Application\Command\CreateReport;
use User\Application\Command\CreateReportHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateReportHandlerTest extends TestCase
{
    // use DatabaseTransactions;

    private CreateReportHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(CreateReportHandler::class);
    }

    public function test__handle_WhenDeleteFlashcardReport_success(): void
    {
        // GIVEN
        \App\Models\Report::factory()->create();
        $user = $this->createUser();
        $command = new CreateReport(
            null,
            'email@email.com',
            ReportType::DELETE_ACCOUNT,
            'description'
        );

        // WHEN
        $this->handler->handle($command);

        // THEN
        $ticket = Report::query()
            ->where([
                'email' => 'email@email.com',
                'user_id' => null,
                'type' => ReportType::DELETE_ACCOUNT,
            ])->first();

        $this->assertNotNull($ticket);
        $this->assertNull($ticket->context);
    }

    public function test__handle_WhenFlashcardReport_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $flashcard = Flashcard::factory()->create();

        $command = new CreateReport(
            $user->getId(),
            'email@email.com',
            ReportType::INAPPROPRIATE_CONTENT,
            'description',
            (string) $flashcard->id,
            ReportableType::FLASHCARD
        );

        // WHEN
        $this->handler->handle($command);

        // THEN
        $ticket = Report::query()
            ->where([
                'email' => 'email@email.com',
                'user_id' => $user->id,
                'type' => ReportType::INAPPROPRIATE_CONTENT,
            ])->first();

        $this->assertNotNull($ticket);
        $this->assertSame($ticket->reportable_id, (string) $flashcard->id);
        $this->assertSame($ticket->reportable_type, ReportableType::FLASHCARD->value);
    }
}
