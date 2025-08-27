<?php

declare(strict_types=1);

use App\Models\User;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\ValueObjects\SessionId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\SessionRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(SessionRepository::class);
});
test('create should create new session', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $domain_deck = $this->domainDeck($deck);
    $session = new Session(
        SessionStatus::STARTED,
        SessionType::UNSCRAMBLE_WORDS,
        $user->getId(),
        10,
        'Mozilla/Firefox',
        $domain_deck,
    );

    $session_id = $this->repository->create($session);

    $this->assertDatabaseHas('learning_sessions', [
        'id' => $session_id->getValue(),
        'user_id' => $user->id,
        'type' => SessionType::UNSCRAMBLE_WORDS->value,
    ]);
});
test('find should find session', function () {
    // GIVEN
    $session = LearningSession::factory()->create([
        'flashcard_deck_id' => null,
    ]);

    // WHEN
    $result = $this->repository->find(new SessionId($session->id));

    expect($result->getId()->getValue())->toBe($session->id);
    expect($result->getUserId()->getValue())->toBe($session->user_id);
    expect($result->getCardsPerSession())->toBe($session->cards_per_session);
});
test('set all user sessions status should change status only for user sessions', function () {
    // GIVEN
    $user = User::factory()->create();
    $user_session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'status' => SessionStatus::STARTED->value,
    ]);
    $other_user_session = LearningSession::factory()->create([
        'status' => SessionStatus::IN_PROGRESS->value,
    ]);
    $expected_status = SessionStatus::FINISHED;

    // WHEN
    $this->repository->setAllOwnerSessionsStatus($user->getId(), $expected_status);

    // THEN
    $this->assertDatabaseHas('learning_sessions', [
        'id' => $user_session->id,
        'status' => $expected_status->value,
    ]);
    $this->assertDatabaseHas('learning_sessions', [
        'id' => $other_user_session->id,
        'status' => $other_user_session->status,
    ]);
});
test('has any session when user has session true', function () {
    // GIVEN
    $user = $this->createUser();
    LearningSession::factory()->create([
        'user_id' => $user->id,
    ]);

    // WHEN
    $result = $this->repository->hasAnySession($user->getId());

    // THEN
    expect($result)->toBeTrue();
});
test('has any session when user does not have session false', function () {
    // GIVEN
    $user = $this->createUser();
    LearningSession::factory()->create([
        'user_id' => $this->createUser()->id,
    ]);

    // WHEN
    $result = $this->repository->hasAnySession($user->getId());

    // THEN
    expect($result)->toBeFalse();
});
test('update status by ids updates only sessions with given ids', function () {
    // GIVEN
    $session_to_not_udpate = LearningSession::factory()->create([
        'status' => SessionStatus::STARTED,
    ]);
    $sessions_to_update = [
        LearningSession::factory()->create(['status' => SessionStatus::STARTED]),
        LearningSession::factory()->create(['status' => SessionStatus::STARTED]),
    ];

    // WHEN
    $this->repository->updateStatusById([
        $sessions_to_update[0]->getId(),
        $sessions_to_update[1]->getId(),
    ], SessionStatus::FINISHED);

    // THEN
    $this->assertDatabaseHas('learning_sessions', [
        'id' => $session_to_not_udpate->id,
        'status' => SessionStatus::STARTED->value,
    ]);
    foreach ($sessions_to_update as $session) {
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $session->id,
            'status' => SessionStatus::FINISHED->value,
        ]);
    }
});
