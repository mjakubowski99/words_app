<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\AiGenerators;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\StoryCollection;

interface IFlashcardGenerator
{
    public function generate(Owner $owner, Deck $deck, FlashcardPrompt $prompt): StoryCollection;
}
