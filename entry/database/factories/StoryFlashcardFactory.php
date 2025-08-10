<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Story;
use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoryFlashcard>
 */
class StoryFlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'story_id' => Story::factory()->create(),
            'flashcard_id' => Flashcard::factory()->create(),
            'sentence_override' => null,
        ];
    }
}
