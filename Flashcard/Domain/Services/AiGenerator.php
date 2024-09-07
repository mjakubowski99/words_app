<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\FlashcardPrompt;
use Flashcard\Domain\Services\AiGenerators\GeminiGenerator;
use Shared\User\IUser;
use Shared\Utils\ValueObjects\UserId;

class AiGenerator
{
    public function __construct(private readonly GeminiGenerator $generator)
    {

    }
    public function generate(IUser $user, string $category): array
    {
        $prompt = new FlashcardPrompt($category);

        $flashcards = $this->generator->generate($prompt);

        foreach ($flashcards as $flashcard) {
            $to_create = new Flashcard();
        }
    }
}