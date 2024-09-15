<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\MainCategory;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\SessionId;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;

class SessionMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function updateStatus(Owner $owner, SessionStatus $status): void
    {
        $this->db::table('learning_sessions')
            ->where('user_id', $owner->getId())
            ->update([
                'status' => $status->value,
            ]);
    }

    public function create(Session $session): SessionId
    {
        $result = $this->db::table('learning_sessions')
            ->insertGetId([
                'user_id' => $session->getOwner()->getId()->getValue(),
                'status' => $session->getStatus()->value,
                'flashcard_category_id' => $session->getFlashcardCategory()->getId()->getValue(),
                'cards_per_session' => $session->getCardsPerSession(),
                'device' => $session->getDevice(),
            ]);

        return new SessionId($result);
    }

    public function update(Session $session): void
    {
        $result = $this->db::table('learning_sessions')
            ->find($session->getId()->getValue());

        if (!$result) {
            throw new \Exception("Not found");
        }

        $this->db::table('learning_sessions')
            ->where('id', $session->getId()->getValue())
            ->update([
                'user_id' => $session->getOwner()->getId()->getValue(),
                'status' => $session->getStatus()->value,
                'flashcard_category_id' => $session->getFlashcardCategory()->getId()->getValue(),
                'cards_per_session' => $session->getCardsPerSession(),
                'device' => $session->getDevice(),
            ]);
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
        $category = $data->category_id === null ? new MainCategory() : (new Category(
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $data->tag,
            $data->name,
        ))->init(new CategoryId($data->category_id));

        return (new Session(
            SessionStatus::from($data->status),
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $data->cards_per_session,
            $data->device,
            $category,
        ))->init(new SessionId($data->id));
    }
}
