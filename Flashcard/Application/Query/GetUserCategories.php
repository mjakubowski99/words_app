<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\User\IUser;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;

class GetUserCategories
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(IUser $user, ?string $search, int $page, int $per_page): array
    {
        return $this->repository->getByUser(
            $user->getId(),
            $user->getUserLanguage()->getEnum(),
            $user->getLearningLanguage()->getEnum(),
            $search,
            $page,
            $per_page
        );
    }
}
