<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command\UpdateRatingsByPreviousRatingHandler;

use App\Models\LearningSession;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\LearningSessionFlashcard;

trait UpdateRatingsByPreviousRatingHandlerTrait
{
    private function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    private function createLearningSessionFlashcard(array $attributes = []): LearningSessionFlashcard
    {
        return LearningSessionFlashcard::factory()->create($attributes);
    }

    private function createLearningSession(array $attributes = []): LearningSession
    {
        return LearningSession::factory()->create($attributes);
    }
}
