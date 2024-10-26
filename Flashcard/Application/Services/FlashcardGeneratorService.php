<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;

use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Application\DTO\ResolvedCategory;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;

class FlashcardGeneratorService
{
    public function __construct(
        private IFlashcardCategoryRepository $category_repository,
        private IFlashcardRepository $repository,
        private IFlashcardGenerator $generator,
    ) {}

    public function generate(ResolvedCategory $category, string $category_name): array
    {
        $prompt = new FlashcardPrompt($category_name);

        try {
            $flashcards = $this->generator->generate($category->getCategory()->getOwner(), $category->getCategory(), $prompt);

            $this->repository->createMany($flashcards);

            return $flashcards;
        } catch (\Throwable $exception) {
            if (!$category->isExistingCategory()) {
                $this->category_repository->removeCategory($category->getCategory());
            }

            throw $exception;
        }
    }
}
