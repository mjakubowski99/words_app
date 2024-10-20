<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Flashcard;
use Shared\Exceptions\ForbiddenException;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;

class CreateFlashcardHandler
{
    public function __construct(
        private IFlashcardCategoryRepository $category_repository,
        private IFlashcardRepository $repository
    ) {}

    public function handle(CreateFlashcard $command): void
    {
        $category = $this->category_repository->findById($command->getCategoryId());

        if (!$category->getOwner()->equals($command->getOwner())) {
            throw new ForbiddenException('You must be category owner to create flashcard');
        }

        $flashcard = new Flashcard(
            FlashcardId::noId(),
            $command->getWord(),
            $command->getWordLang(),
            $command->getTranslation(),
            $command->getTranslationLang(),
            $command->getContext(),
            $command->getContextTranslation(),
            $command->getOwner(),
            $category,
        );

        $this->repository->createMany([$flashcard]);
    }
}
