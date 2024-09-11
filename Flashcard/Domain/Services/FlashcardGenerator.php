<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Repositories\IFlashcardRepository;
use Flashcard\Domain\Services\Generators\IFlashcardGenerator;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Shared\Utils\ValueObjects\Language;

class FlashcardGenerator
{
    public function __construct(
        private IFlashcardGenerator $generator,
        private IFlashcardRepository $repository,
        private IFlashcardCategoryRepository $category_repository,
    ) {}

    public function withAI(Owner $owner, string $category_name): ICategory
    {
        $prompt = new FlashcardPrompt($category_name, Language::from(Language::EN), Language::from(Language::PL));

        $category = new Category($owner, mb_strtolower($category_name), $category_name);

        $category = $this->category_repository->createCategory($category);

        if (!$category instanceof Category) {
            throw new \UnexpectedValueException();
        }

        $flashcards = $this->generator->generate($owner, $category, $prompt);

        $this->repository->createMany($flashcards);

        return $category;
    }
}
