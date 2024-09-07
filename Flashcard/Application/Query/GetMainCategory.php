<?php

declare(strict_types=1);

namespace Flashcard\Application\Query;

use Flashcard\Application\DTO\MainFlashcardCategoryDTO;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;

class GetMainCategory
{
    public function __construct(private IFlashcardCategoryRepository $repository) {}

    public function handle(): MainFlashcardCategoryDTO
    {
        $category = $this->repository->findMain();

        if (!$category->isMainCategory()) {
            throw new \UnexpectedValueException('Given category is not main category');
        }

        return new MainFlashcardCategoryDTO(
            $category->getId(),
            $category->getName()
        );
    }
}
