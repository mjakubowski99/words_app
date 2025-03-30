<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\FlashcardDeck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashcardDeckActivity>
 */
class FlashcardDeckActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flashcard_deck_id' => fn () => FlashcardDeck::factory()->create(),
            'user_id' => fn () => User::factory()->create(),
            'last_viewed_at' => now(),
        ];
    }
}
