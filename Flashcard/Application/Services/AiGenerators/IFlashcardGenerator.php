<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\AiGenerators;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPrompt;

interface IFlashcardGenerator
{
    /** @return Flashcard[] */
    public function generate(Owner $owner, Category $category, FlashcardPrompt $prompt): array;
}
