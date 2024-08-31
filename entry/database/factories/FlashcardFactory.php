<?php

namespace Database\Factories;

use App\Models\FlashcardCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Utils\ValueObjects\Language;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'flashcard_category_id' => FlashcardCategory::factory()->create(),
            'user_id' => fn() => User::factory()->create(),
            'word' => $this->faker->name,
            'word_lang' => Language::PL,
            'translation' => $this->faker->name,
            'translation_lang' => Language::EN,
            'context' => 'Context',
            'context_translation' => 'Context translation',
        ];
    }
}
