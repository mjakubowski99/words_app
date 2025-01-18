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
        $response = $this->actingAs($user)
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
        $response = $this->actingAs($user)
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
                        'language_level',
                        'emoji',
                    ],
                ],
            ],
        ]);
    }

    public function test__bulkDelete_UserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $decks = [
            $this->createFlashcardDeck()->getId()->getValue(),
            $this->createFlashcardDeck()->getId()->getValue(),
        ];

        // WHEN
        $response = $this->actingAs($user)
            ->json('DELETE', route('v2.flashcards.decks.bulk-delete'), [
                'flashcard_deck_ids' => $decks,
            ]);

        // THEN
        $response->assertStatus(204);
    }

    public function test__bulkDelete_UserUnauthorized_fail(): void
    {
        // GIVEN

        // WHEN
        $response = $this
            ->json('DELETE', route('v2.flashcards.decks.bulk-delete'), [
                'flashcard_deck_ids' => [],
            ]);

        // THEN
        $response->assertStatus(401);
    }

    public function test__bulkDelete_EmptyDecks_fail(): void
    {
        // GIVEN
        $user = $this->createUser();

        // WHEN
        $response = $this->actingAs($user)
            ->json('DELETE', route('v2.flashcards.decks.bulk-delete'), [
                'flashcard_deck_ids' => [],
            ]);

        // THEN
        $response->assertStatus(422);
    }

    public function test__store_UserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();

        // WHEN
        $response = $this->actingAs($user)
            ->json('POST', route('v2.flashcards.decks.store'), [
                'name' => 'Name',
                'default_language_level' => LanguageLevel::C2,
            ]);

        // THEN
        $response->assertStatus(200);
        $this->assertDatabaseHas('flashcard_decks', [
            'user_id' => $user->id,
            'admin_id' => null,
            'name' => 'Name',
            'default_language_level' => LanguageLevel::C2->value,
        ]);
    }

    public function test__update_UserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $deck = $this->createUserDeck($user, [
            'name' => 'Name',
            'default_language_level' => LanguageLevel::C2->value,
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->json('PUT', route('v2.flashcards.decks.update', ['flashcard_deck_id' => $deck->id]), [
                'name' => 'Name 2',
            ]);

        // THEN
        $response->assertStatus(200);
        $this->assertDatabaseHas('flashcard_decks', [
            'id' => $deck->getId()->getValue(),
            'user_id' => $user->id,
            'admin_id' => null,
            'name' => 'Name 2',
            'default_language_level' => LanguageLevel::C2->value,
        ]);
    }

    public function test__update_UserNotDeckOwner_unauthorized(): void
    {
        // GIVEN
        $user = $this->createUser();
        $deck = $this->createFlashcardDeck();

        // WHEN
        $response = $this->actingAs($user)
            ->json('PUT', route('v2.flashcards.decks.update', ['flashcard_deck_id' => $deck->id]), [
                'name' => 'Name 2',
            ]);

        // THEN
        $response->assertStatus(403);
    }
}
