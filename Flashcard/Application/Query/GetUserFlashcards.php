<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\Repository\IFlashcardReadRepository;

class GetUserFlashcards
{
    public function __construct(
        private IFlashcardReadRepository $repository
    ) {}

    public function get(UserId $user_id, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        return $this->repository->findByUser($user_id, $search, $page, $per_page);
    }
}
