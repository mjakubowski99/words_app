<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Models\Owner;
use Flashcard\Application\Repository\IFlashcardCategoryReadRepository;

class GetUserCategories
{
    public function __construct(private IFlashcardCategoryReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(Owner $owner, int $page, int $per_page): array
    {
        return $this->repository->getByOwner($owner, $page, $per_page);
    }
}
