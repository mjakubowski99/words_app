<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Application\Repository\ISessionFlashcardRepository;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Mappers\SessionFlashcardMapper;

class SessionFlashcardRepository implements ISessionFlashcardRepository
{
    public function __construct(
        private readonly SessionFlashcardMapper $session_flashcard_mapper,
    ) {}

    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array
    {
        return $this->session_flashcard_mapper->getLatestSessionFlashcardIds($session_id, $limit);
    }
}