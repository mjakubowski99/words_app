<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\DTO\FlashcardSetDTO;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Repositories\IFlashcardRepository;

class GetUnratedSessionFlashcardsHandler
{
    public function __construct(
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function handle(SessionId $session_id, int $limit): FlashcardSetDTO
    {
        return new FlashcardSetDTO(
            $this->flashcard_repository->getNotRatedSessionFlashcards($session_id, $limit)
        );
    }
}