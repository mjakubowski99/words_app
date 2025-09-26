<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Enum\LanguageLevel;
use Shared\User\IUser;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;

class GetAdminDecks
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(IUser $user, ?LanguageLevel $level, ?string $search, int $page, int $per_page): array
    {
        return $this->repository->getAdminDecks(
            $user->getId(),
            $user->getUserLanguage()->getEnum(),
            $user->getLearningLanguage()->getEnum(),
            $level,
            $search,
            $page,
            $per_page
        );
    }
}
