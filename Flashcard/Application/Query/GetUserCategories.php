<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;

class GetUserCategories
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(UserId $user_id, ?string $search, int $page, int $per_page): array
    {
        return $this->repository->getByUser($user_id, $search, $page, $per_page);
    }
}
