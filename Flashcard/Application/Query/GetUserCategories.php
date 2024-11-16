<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;

class GetUserCategories
{
    public function __construct(private IFlashcardDeckReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(Owner $owner, ?string $search, int $page, int $per_page): array
    {
        return $this->repository->getByOwner($owner, $search, $page, $per_page);
    }
}
