<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\FlashcardDeck;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'flashcard_deck_id' => FlashcardDeck::factory()->create(),
            'user_id' => fn () => User::factory()->create(),
            'front_word' => $this->faker->name,
            'front_lang' => Language::PL,
            'back_word' => $this->faker->name,
            'back_lang' => Language::EN,
            'front_context' => 'Context',
            'back_context' => 'Context translation',
        ];
    }
}
