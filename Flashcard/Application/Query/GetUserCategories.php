<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\Repository\IFlashcardCategoryReadRepository;

class GetUserCategories
{
    public function __construct(private IFlashcardCategoryReadRepository $repository) {}

    /** @return OwnerCategoryRead[] */
    public function handle(Owner $owner, ?string $search, int $page, int $per_page): array
    {
        return $this->repository->getByOwner($owner, $search, $page, $per_page);
    }
}
