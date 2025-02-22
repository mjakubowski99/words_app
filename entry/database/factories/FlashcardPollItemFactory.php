<?php

namespace Database\Factories;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashcardPollItem>
 */
class FlashcardPollItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'flashcard_id' => fn() => Flashcard::factory()->create(),
            'user_id' => fn() => User::factory()->create(),
            'easy_ratings_count' => random_int(0, 10),
            'easy_ratings_count_to_purge' => random_int(11, 20),
            'leitner_level' => 0,
        ];
    }
}
