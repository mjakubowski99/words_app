<?php

namespace Flashcard\Application\Query;

use Flashcard\Application\DTO\FlashcardCategoryDetailsDTO;
use Flashcard\Application\DTO\FlashcardDTO;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\ValueObjects\CategoryId;
use Shared\Utils\ValueObjects\UserId;

class GetCategoryDetails
{
    public function __construct(
        private IFlashcardCategoryRepository $repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function get(CategoryId $id): FlashcardCategoryDetailsDTO
    {
        $category = $this->repository->findById($id);

        return new FlashcardCategoryDetailsDTO(
            $category->getId(),
            $category->getName(),
            new UserId($category->getOwner()->getId()->getValue()),
            array_map(function (Flashcard $flashcard) {
                return new FlashcardDTO(
                    $flashcard->getId(),
                    $flashcard->getWord(),
                    $flashcard->getWordLang(),
                    $flashcard->getTranslation(),
                    $flashcard->getTranslationLang(),
                    $flashcard->getContext(),
                    $flashcard->getContextTranslation()
                );
            }, $this->flashcard_repository->getByCategory($id))
        );
    }
}