<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Infrastructure\Mappers\NextSessionFlashcardsMapper;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;

class NextSessionFlashcardsRepository implements INextSessionFlashcardsRepository
{
    public function __construct(
        private readonly NextSessionFlashcardsMapper $mapper
    ) {}

    public function find(SessionId $id): NextSessionFlashcards
    {
        return $this->mapper->find($id);
    }

    public function save(NextSessionFlashcards $next_session_flashcards): void
    {
        $this->mapper->save($next_session_flashcards);
    }
}
