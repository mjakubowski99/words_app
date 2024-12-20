<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Infrastructure\Http\Controller\v2;

use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FlashcardDeckControllerTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    public function test__index_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $this->createFlashcardDeck(['user_id' => $user->id]);
        $this->createFlashcardDeck(['user_id' => $user->id]);

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->json('GET', route('v2.flashcards.decks.index'), [
                'page' => 1,
                'per_page' => 15,
            ]);

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'decks' => [
                    '*' => [
                        'id',
                        'name',
                        'language_level',
                        'flashcards_count',
                        'last_learnt_at',
                        'rating_percentage',
                    ],
                ],
                'page',
                'per_page',
            ],
        ]);
    }

    public function test__ratingStatsRead_WhenUserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $deck = $this->createFlashcardDeck();
        $flashcard = $this->createFlashcard([
            'flashcard_deck_id' => $deck->id,
        ]);
        $this->createLearningSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::WEAK->value,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('GET', route('v2.flashcards.decks.rating-stats', [
                'flashcard_deck_id' => $deck->id,
            ]));

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'rating',
                    'rating_percentage',
                ],
            ],
        ]);
    }
}
