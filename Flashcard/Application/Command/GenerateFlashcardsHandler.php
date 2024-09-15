<?php

namespace Flashcard\Application\Command;

use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\ValueObjects\CategoryId;
use Shared\Utils\ValueObjects\Language;

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
            Language::from(Language::EN),
            Language::from(Language::PL)
        );
        $category = new Category($command->getOwner(), mb_strtolower($command->getCategoryName()), $command->getCategoryName());

        $category = $this->category_repository->createCategory($category);

        $flashcards = $this->generator->generate($command->getOwner(), $category, $prompt);

        $this->repository->createMany($flashcards);

        return $category->getId();
    }
}