<?php

declare(strict_types=1);

namespace Flashcard\Application\Command;

use Flashcard\Domain\Models\Category;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;

class GenerateFlashcardsHandler
{
    public function __construct(
        private readonly IFlashcardRepository $repository,
        private readonly IFlashcardCategoryRepository $category_repository,
        private readonly IFlashcardGenerator $generator,
    ) {}

    public function handle(GenerateFlashcards $command): CategoryId
    {
        $prompt = new FlashcardPrompt(
            $command->getCategoryName(),
            Language::from(Language::PL),
            Language::from(Language::EN)
        );
        $category = new Category($command->getOwner(), mb_strtolower($command->getCategoryName()), $command->getCategoryName());

        $category = $this->category_repository->createCategory($category);

        if (!$category instanceof Category) {
            throw new \Exception('Invalid category type');
        }

        $flashcards = $this->tryToGenerateFlashcards($command, $category, $prompt);

        $this->repository->createMany($flashcards);

        return $category->getId();
    }

    private function tryToGenerateFlashcards(GenerateFlashcards $command, Category $category, FlashcardPrompt $prompt): array
    {
        try {
            return $this->generator->generate($command->getOwner(), $category, $prompt);
        } catch (\Throwable $exception) {
            $this->category_repository->removeCategory($category);

            throw $exception;
        }
    }
}
