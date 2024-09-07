<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Shared\Enum\SessionStatus;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\FlashcardCategory;

class SessionMapper
{
    public const COLUMNS = ['id', 'user_id', 'status', 'cards_per_session', 'device'];

    public function __construct(
        private readonly DB $db,
    ) {}

    public function updateStatus(UserId $user_id, SessionStatus $status): void
    {
        $this->db::table('learning_sessions')
            ->where('user_id', $user_id->getValue())
            ->update([
                'status' => $status->value,
            ]);
    }

    public function create(Session $session): SessionId
    {
        $result = $this->db::table('learning_sessions')
            ->insertGetId([
                'user_id' => $session->getUserId()->getValue(),
                'status' => $session->getStatus()->value,
                'flashcard_category_id' => $session->getFlashcardCategory()->getId()->getValue(),
                'cards_per_session' => $session->getCardsPerSession(),
                'device' => $session->getDevice(),
            ]);

        return new SessionId($result);
    }

    public function find(SessionId $id): Session
    {
        $result = $this->db::table('learning_sessions')
            ->where('learning_sessions.id', $id->getValue())
            ->join('flashcard_categories', 'flashcard_categories.id', '=', 'learning_sessions.flashcard_category_id')
            ->select(
                'learning_sessions.id',
                'learning_sessions.user_id',
                'learning_sessions.status',
                'learning_sessions.cards_per_session',
                'learning_sessions.device',
                'flashcard_categories.id as category_id',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag',
                'flashcard_categories.name',
            )
            ->first();

        return $this->map($result);
    }

    public function map(object $data): Session
    {
        return (new Session(
            SessionStatus::from($data->status),
            new UserId($data->user_id),
            $data->cards_per_session,
            $data->device,
            (new FlashcardCategory(
                new UserId($data->user_id),
                $data->tag,
                $data->name,
            ))->init(new CategoryId($data->category_id))
        ))->init(new SessionId($data->id));
    }
}
