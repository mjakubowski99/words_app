<?php

namespace Flashcard\Domain\Services\Generators;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Models\Owner;

interface IFlashcardGenerator
{
    /** @return Flashcard[] */
    public function generate(Owner $owner, Category $category, FlashcardPrompt $prompt): array;
}