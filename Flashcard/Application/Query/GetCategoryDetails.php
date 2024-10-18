<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\ReadModels\CategoryDetailsRead;
use Flashcard\Application\Repository\IFlashcardCategoryReadRepository;

class GetCategoryDetails
{
    public function __construct(
        private IFlashcardCategoryReadRepository $repository,
    ) {}

    public function get(CategoryId $id, ?int $limit): CategoryDetailsRead
    {
        return $this->repository->findDetails($id, $limit);
    }
}
