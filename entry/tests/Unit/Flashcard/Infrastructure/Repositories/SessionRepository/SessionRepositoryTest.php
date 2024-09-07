<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\SessionRepository;

use App\Models\User;
use Shared\Enum\SessionStatus;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SessionId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\SessionRepository;

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
        $category = FlashcardCategory::factory()->create();
        $domain_category = $this->domainCategory($category);
        $session = new Session(
            SessionStatus::STARTED,
            $this->createUserId($user),
            10,
            'Mozilla/Firefox',
            $domain_category,
        );

        $session_id = $this->repository->create($session);

        $this->assertDatabaseHas('learning_sessions', [
            'id' => $session_id->getValue(),
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function find_ShouldFindSession(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();

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
        $user_id = new UserId($user->id);
        $user_session = LearningSession::factory()->create([
            'user_id' => $user->id,
            'status' => SessionStatus::STARTED->value,
        ]);
        $other_user_session = LearningSession::factory()->create([
            'status' => SessionStatus::IN_PROGRESS->value,
        ]);
        $expected_status = SessionStatus::FINISHED;

        // WHEN
        $this->repository->setAllUserSessionsStatus($user_id, $expected_status);

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
}
