<?php

declare(strict_types=1);

namespace Smoke\Flashcard\Infrastructure\Http\v2;

use Shared\Enum\LanguageLevel;
use Tests\Traits\GeminiApiFaker;
use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FlashcardDeckControllerTest extends FlashcardTestCase
{
    use DatabaseTransactions;
    use GeminiApiFaker;

    public function test__index_WhenUserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $this->createFlashcardDeck(['user_id' => $user->id]);

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->json('GET', route('v2.flashcards.decks.index'));

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'decks' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
                'page',
                'per_page',
            ],
        ]);
    }

    public function test__index_WhenUserUnauthorized_fail(): void
    {
        // GIVEN
        $user = $this->createUser();

        // WHEN
        $response = $this
            ->json('GET', route('v2.flashcards.decks.index'));

        // THEN
        $response->assertStatus(401);
    }

    public function test__generateFlashcards_UserNotAuthorized_unauthorized(): void
    {
        // GIVEN
        $this->mockGeminiApiGenerateFlashcards();

        // WHEN
        $response = $this
            ->json('POST', route('v2.flashcards.decks.generate-flashcards'), [
                'category_name' => 'Category',
            ]);

        // THEN
        $response->assertStatus(401);
    }

    public function test_mergeFlashcards_UserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
        $to_deck = $this->createFlashcardDeck(['user_id' => $user->id]);

        // WHEN
        $response = $this
            ->actingAs($user)
            ->json(
                'POST',
                route('v2.flashcards.decks.merge-flashcards', [
                    'from_deck_id' => $from_deck->id,
                    'to_deck_id' => $to_deck->id,
                ]),
                [
                    'new_name' => 'New name',
                ]
            );

        // THEN
        $response->assertStatus(204);
    }

    public function test__generateFlashcards_UserAuthorized_success(): void
    {
        // GIVEN
        $this->mockGeminiApiGenerateFlashcards();

        $user = $this->createUser();

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->json('POST', route('v2.flashcards.decks.generate-flashcards'), [
                'category_name' => 'Category',
                'language_level' => LanguageLevel::C1,
            ]);

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'flashcards' => [
                    '*' => [
                        'id',
                        'front_word',
                        'front_lang',
                        'back_word',
                        'back_lang',
                        'front_context',
                        'back_context',
                    ],
                ],
            ],
        ]);
    }
}
