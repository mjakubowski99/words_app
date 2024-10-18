<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Application\DTO\ResolvedCategory;
use Flashcard\Application\Services\CategoryResolver;
use Flashcard\Application\DTO\GenerateFlashcardsResult;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;

final readonly class GenerateFlashcardsHandler
{
    public function __construct(
        private IFlashcardRepository $repository,
        private IFlashcardCategoryRepository $category_repository,
        private IFlashcardGenerator $generator,
        private CategoryResolver $category_resolver,
    ) {}

    public function handle(GenerateFlashcards $command): GenerateFlashcardsResult
    {
        $prompt = new FlashcardPrompt($command->getCategoryName());

        $resolved_category = $this->category_resolver->resolve($command->getOwner(), $command->getCategoryName());

        $flashcards = $this->tryToGenerateFlashcardsForNewCategory($command, $resolved_category, $prompt);

        $this->repository->createMany($flashcards);

        return new GenerateFlashcardsResult(
            $resolved_category->getCategory()->getId(),
            count($flashcards),
            $resolved_category->isExistingCategory()
        );
    }

    private function tryToGenerateFlashcardsForNewCategory(GenerateFlashcards $command, ResolvedCategory $resolved_category, FlashcardPrompt $prompt): array
    {
        $category = $resolved_category->getCategory();

        try {
            return $this->generator->generate($command->getOwner(), $category, $prompt);
        } catch (\Throwable $exception) {
            if (!$resolved_category->isExistingCategory()) {
                $this->category_repository->removeCategory($category);
            }

            throw $exception;
        }
    }
}
