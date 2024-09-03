<?php

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Infrastructure\Repositories\Mappers\FlashcardCategoryMapper;
use Flashcard\Infrastructure\Repositories\Mappers\SessionMapper;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;

class SessionRepository implements ISessionRepository
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionMapper $session_mapper,
    ) {}

    public function getNotRatedFlashcards(SessionId $session_id): array
    {
        // TODO: Implement getNotRatedFlashcards() method.
    }

    public function getNotRatedFlashcardsCount(SessionId $session_id): int
    {
        // TODO: Implement getNotRatedFlashcardsCount() method.
    }

    public function setAllUserSessionsStatus(UserId $user_id, SessionStatus $status): void
    {
        $this->newSessionQuery()
            ->where('user_id', $user_id->getValue())
            ->update([
                'status' => $status->value
            ]);
    }

    public function getRatedFlashcardsCount(SessionId $session_id): int
    {
        // TODO: Implement getRatedFlashcardsCount() method.
    }

    public function existsActiveByCategory(UserId $user_id, CategoryId $category_id): bool
    {
        // TODO: Implement existsActiveByCategory() method.
    }

    public function create(Session $session): SessionId
    {
        $session_id = $this->newSessionQuery()
            ->insertGetId([
                'user_id' => $session->getUserId()->getValue(),
                'status' => $session->getStatus()->value,
                'flashcard_category_id' => $session->getFlashcardCategory()->getId()->getValue(),
                'cards_per_session' => $session->getCardsPerSession(),
                'device' => $session->getDevice()
            ]);

        return new SessionId($session_id);
    }

    public function find(SessionId $id): Session
    {
        $db_session = (array) $this->newSessionQuery()
            ->where('learning_sessions.id', $id->getValue())
            ->join('flashcard_categories', 'flashcard_categories.id', '=', 'learning_sessions.flashcard_category_id')
            ->select(
                'learning_sessions.id as learning_sessions_id',
                'learning_sessions.user_id as learning_sessions_user_id',
                'learning_sessions.status as learning_sessions_status',
                'learning_sessions.cards_per_session as learning_sessions_cards_per_session',
                'learning_sessions.device as learning_sessions_device',
                'flashcard_categories.id as flashcard_categories_id',
                'flashcard_categories.user_id as flashcard_categories_user_id',
                'flashcard_categories.tag as flashcard_categories_tag',
                'flashcard_categories.name as flashcard_categories_name',
            )
            ->first();

        return $this->session_mapper->map($db_session);
    }

    private function newSessionQuery(): Builder
    {
        return $this->db::table('learning_sessions');
    }
}