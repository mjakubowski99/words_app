<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\SessionService;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Services\SessionService;
use Mockery\MockInterface;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Uuid;
use Tests\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionService $service;

    private ISessionRepository|MockInterface $session_repository;
    private IFlashcardCategoryRepository|MockInterface $flashcard_category_repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->flashcard_category_repository = $this->mockery(IFlashcardCategoryRepository::class);
        $this->session_repository = $this->mockery(ISessionRepository::class);
        $this->service = $this->app->make(SessionService::class, [
            'session_repository' => $this->session_repository,
            'category_repository' => $this->flashcard_category_repository,
        ]);
    }

    /**
     * @test
     */
    public function newSession_ShouldCreateNewSession(): void
    {
        // GIVEN
        $user_id = UserId::fromString(Uuid::make()->getValue());
        $category_id = new CategoryId('1');
        $cards_per_session = 5;
        $device = 'Mozilla/Firefox';

        $category = new FlashcardCategory($user_id, 'tag', 'name');
        $session_repo_expectation = $this->session_repository
            ->shouldReceive('setAllUserSessionsStatus')
            ->withArgs(function(UserId $user_id, SessionStatus $status) {
                $this->assertSame(SessionStatus::STARTED, $status);
                return true;
            })
            ->andReturn();
        $flashcard_repo_expectation = $this->flashcard_category_repository->shouldReceive('findById')->andReturn($category);

        // WHEN
        $session = $this->service->newSession($user_id, $category_id, $cards_per_session, $device);

        // THEN
        $this->assertSame($cards_per_session, $session->getCardsPerSession());
        $this->assertSame($device, $session->getDevice());
        $this->assertSame($user_id, $session->getUserId());
        $this->assertFalse($session->isFinished());
        $this->assertSame(SessionStatus::STARTED, $session->getStatus());
        $this->assertSame($category, $session->getFlashcardCategory());

        $session_repo_expectation->once();
        $flashcard_repo_expectation->once();
    }
}
