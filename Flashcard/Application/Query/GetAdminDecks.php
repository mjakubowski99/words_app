<?php

namespace Flashcard\Application\Query;

use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;
use Shared\Utils\ValueObjects\UserId;

class GetAdminDecks
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(UserId $user_id, ?string $search, int $page, int $per_page): array
    {
        return $this->repository->getAdminDecks($user_id, $search, $page, $per_page);
    }
}