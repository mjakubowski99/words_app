<?php

declare(strict_types=1);

use App\Models\User;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use Tests\Base\FlashcardTestCase;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Application\Command\CreateSession;
use Flashcard\Application\Command\CreateSessionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->command_handler = $this->app->make(CreateSessionHandler::class);
});
test('create session handler should create session', function () {
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
    expect($result->success())->toBeTrue();
});
test('create session handler user is not deck owner fail', function () {
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
});
