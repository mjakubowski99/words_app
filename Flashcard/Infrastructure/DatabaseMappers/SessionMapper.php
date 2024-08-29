<?php

namespace Flashcard\Infrastructure\DatabaseMappers;

use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Models\SessionId;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;

class SessionMapper
{
    public const COLUMNS = ['id', 'user_id', 'status', 'cards_per_session', 'device'];

    public function __construct(
        private FlashcardCategoryMapper $flashcard_category_mapper
    ) {}

    public function map(array $data): Session
    {
        $category = $this->flashcard_category_mapper->map($data);

        $session = new Session(
            SessionStatus::from($data['learning_sessions_status']),
            new UserId($data['learning_sessions_user_id']),
            $data['learning_sessions_cards_per_session'],
            $data['learning_sessions_device'],
            $category
        );
        $session->setId(SessionId::fromInt($data['learning_sessions_id']));

        return $session;
    }
}