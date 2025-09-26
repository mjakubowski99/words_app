<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\User\IUser;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\Repository\IFlashcardReadRepository;

class GetUserFlashcards
{
    public function __construct(
        private IFlashcardReadRepository $repository
    ) {}

    public function get(IUser $user, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        return $this->repository->findByUser(
            $user->getId(),
            $user->getUserLanguage()->getEnum(),
            $user->getLearningLanguage()->getEnum(),
            $search,
            $page,
            $per_page
        );
    }
}
