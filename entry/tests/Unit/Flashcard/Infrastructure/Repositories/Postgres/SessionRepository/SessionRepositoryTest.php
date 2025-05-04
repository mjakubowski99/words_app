<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\SessionRepository;

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

class SessionRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private SessionRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(SessionRepository::class);
    }

    /**
     * @test
     */
    public function create_ShouldCreateNewSession(): void
    {
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
    }

    /**
     * @test
     */
    public function find_ShouldFindSession(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create([
            'flashcard_deck_id' => null,
        ]);

        // WHEN
        $result = $this->repository->find(new SessionId($session->id));

        $this->assertSame($session->id, $result->getId()->getValue());
        $this->assertSame($session->user_id, $result->getUserId()->getValue());
        $this->assertSame($session->cards_per_session, $result->getCardsPerSession());
    }

    /**
     * @test
     */
    public function setAllUserSessionsStatus_ShouldChangeStatusOnlyForUserSessions(): void
    {
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
    }

    /**
     * @test
     */
    public function hasAnySession_WhenUserHasSession_true(): void
    {
        // GIVEN
        $user = $this->createUser();
        LearningSession::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $result = $this->repository->hasAnySession($user->getId());

        // THEN
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function hasAnySession_WhenUserDoesNotHaveSession_false(): void
    {
        // GIVEN
        $user = $this->createUser();
        LearningSession::factory()->create([
            'user_id' => $this->createUser()->id,
        ]);

        // WHEN
        $result = $this->repository->hasAnySession($user->getId());

        // THEN
        $this->assertFalse($result);
    }
}
