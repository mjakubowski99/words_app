<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Enum\SessionType;
use Shared\Enum\LanguageLevel;
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Deck;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class SessionMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
    ) {}

    public function findSessionIdsForSessionFlashcardIds(array $session_flashcard_ids): array
    {
        $session_flashcard_ids = array_map(fn ($id) => $id->getValue(), $session_flashcard_ids);

        $session_ids = $this->db::table('learning_session_flashcards')
            ->whereIn('id', $session_flashcard_ids)
            ->select('learning_session_flashcards.learning_session_id')
            ->pluck('learning_session_id');

        return array_map(
            fn ($id) => new SessionId($id),
            $session_ids->toArray()
        );
    }

    public function updateStatusByIds(array $session_ids, SessionStatus $status): void
    {
        $this->db::table('learning_sessions')
            ->whereIn('id', $session_ids)
            ->update([
                'status' => $status->value,
                'updated_at' => now(),
            ]);
    }

    public function updateStatus(UserId $user_id, SessionStatus $status): void
    {
        $this->db::table('learning_sessions')
            ->where('user_id', $user_id->getValue())
            ->update([
                'status' => $status->value,
                'updated_at' => now(),
            ]);
    }

    public function create(Session $session): SessionId
    {
        $now = now();

        $result = $this->db::table('learning_sessions')
            ->insertGetId([
                'user_id' => $session->getUserId()->getValue(),
                'status' => $session->getStatus()->value,
                'flashcard_deck_id' => $session->hasFlashcardDeck() ? $session->getDeck()->getId() : null,
                'cards_per_session' => $session->getCardsPerSession(),
                'device' => $session->getDevice(),
                'type' => $session->getType(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        return new SessionId($result);
    }

    public function update(Session $session): void
    {
        $now = now();

        $result = $this->db::table('learning_sessions')
            ->find($session->getId()->getValue());

        if (!$result) {
            throw new \Exception('Not found');
        }

        $this->db::table('learning_sessions')
            ->where('id', $session->getId()->getValue())
            ->update([
                'user_id' => $session->getUserId()->getValue(),
                'status' => $session->getStatus()->value,
                'flashcard_deck_id' => $session->hasFlashcardDeck() ? $session->getDeck()->getId() : null,
                'cards_per_session' => $session->getCardsPerSession(),
                'device' => $session->getDevice(),
                'updated_at' => $now,
            ]);
    }

    public function find(SessionId $id): Session
    {
        $result = $this->db::table('learning_sessions')
            ->where('learning_sessions.id', $id->getValue())
            ->leftJoin('flashcard_decks', 'flashcard_decks.id', '=', 'learning_sessions.flashcard_deck_id')
            ->select(
                'learning_sessions.id',
                'learning_sessions.type',
                'learning_sessions.user_id',
                'learning_sessions.status',
                'learning_sessions.cards_per_session',
                'learning_sessions.device',
                'flashcard_decks.id as deck_id',
                'flashcard_decks.user_id as deck_user_id',
                'flashcard_decks.admin_id as deck_admin_id',
                'flashcard_decks.tag as deck_tag',
                'flashcard_decks.name as deck_name',
                'flashcard_decks.default_language_level as deck_default_language_level',
            )
            ->first();

        if (!$result) {
            throw new ModelNotFoundException('Session not found exception');
        }

        return $this->map($result);
    }

    public function deleteAllForUser(UserId $user_id): void
    {
        $this->db::table('learning_sessions')
            ->where('user_id', $user_id)
            ->delete();
    }

    public function hasAnySessions(UserId $user_id): bool
    {
        return $this->db::table('learning_sessions')
            ->where('user_id', $user_id)
            ->exists();
    }

    public function map(object $data): Session
    {
        $deck = $data->deck_id === null ? null : (new Deck(
            $this->buildOwner((string) $data->deck_user_id, (string) $data->deck_admin_id),
            $data->deck_tag,
            $data->deck_name,
            LanguageLevel::from($data->deck_default_language_level)
        ))->init(new FlashcardDeckId($data->deck_id));

        return (new Session(
            SessionStatus::from($data->status),
            SessionType::from($data->type),
            new UserId($data->user_id),
            $data->cards_per_session,
            $data->device,
            $deck,
        ))->init(new SessionId($data->id));
    }
}
