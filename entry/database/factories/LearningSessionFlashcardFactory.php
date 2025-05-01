<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Flashcard;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearningSessionFlashcardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'learning_session_id' => fn () => LearningSession::factory()->create(),
            'flashcard_id' => fn () => Flashcard::factory()->create(),
            'rating' => Rating::WEAK->value,
            'affects_progress' => true,
        ];
    }
}
