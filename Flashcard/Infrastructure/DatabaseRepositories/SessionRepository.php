<?php

namespace Flashcard\Infrastructure\DatabaseRepositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Infrastructure\DatabaseMappers\FlashcardCategoryMapper;
use Flashcard\Infrastructure\DatabaseMappers\SessionMapper;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Shared\Enum\SessionStatus;
use Shared\Utils\Str\IStr;
use Shared\Utils\ValueObjects\UserId;

class SessionRepository extends AbstractRepository implements ISessionRepository
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
                ...$this->dbPrefix('learning_sessions', SessionMapper::COLUMNS),
                ...$this->dbPrefix('flashcard_categories', FlashcardCategoryMapper::COLUMNS),
            )
            ->first();

        return $this->session_mapper->map($db_session);
    }

    private function newSessionQuery(): Builder
    {
        return $this->db::table('learning_sessions');
    }
}