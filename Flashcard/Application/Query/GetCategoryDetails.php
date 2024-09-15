<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\ReadModels\CategoryDetailsRead;
use Flashcard\Application\Repository\IFlashcardCategoryReadRepository;
use Flashcard\Domain\ValueObjects\CategoryId;

class GetCategoryDetails
{
    public function __construct(
        private IFlashcardCategoryReadRepository $repository,
    ) {}

    public function get(CategoryId $id): CategoryDetailsRead
    {
        return $this->repository->find($id);
    }
}