<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Application\Services\CategoryResolver;
use Flashcard\Application\DTO\GenerateFlashcardsResult;
use Flashcard\Application\Services\FlashcardGeneratorService;

final readonly class GenerateFlashcardsHandler
{
    public function __construct(
        private CategoryResolver $category_resolver,
        private FlashcardGeneratorService $flashcard_generator_service,
    ) {}

    public function handle(GenerateFlashcards $command): GenerateFlashcardsResult
    {
        $resolved_category = $this->category_resolver->resolveByName($command->getOwner(), $command->getCategoryName());

        $flashcards = $this->flashcard_generator_service->generate($resolved_category, $command->getCategoryName());

        return new GenerateFlashcardsResult(
            $resolved_category->getCategory()->getId(),
            count($flashcards),
            $resolved_category->isExistingCategory()
        );
    }
}
