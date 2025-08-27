<?php

declare(strict_types=1);

use App\Models\Flashcard;
use Shared\Enum\ReportType;
use Shared\Enum\ReportableType;
use User\Infrastructure\Entities\Report;
use User\Application\Command\CreateReport;
use User\Application\Command\CreateReportHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->handler = $this->app->make(CreateReportHandler::class);
});

test('handle when delete flashcard report success', function () {
    // GIVEN
    App\Models\Report::factory()->create();
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

    expect($ticket)->not->toBeNull();
    expect($ticket->context)->toBeNull();
});
test('handle when flashcard report success', function () {
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

    expect($ticket)->not->toBeNull();
    expect((string) $flashcard->id)->toBe($ticket->reportable_id);
    expect(ReportableType::FLASHCARD->value)->toBe($ticket->reportable_type);
});
