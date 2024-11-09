<?php

declare(strict_types=1);

namespace Tests\Smoke\Flashcard;

use Tests\Traits\GeminiApiFaker;
use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FlashcardCategoryControllerTest extends FlashcardTestCase
{
    use DatabaseTransactions;
    use GeminiApiFaker;

    public function test__index_WhenUserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $this->createFlashcardCategory(['user_id' => $user->id]);

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->json('GET', route('flashcards.categories.index'));

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'categories' => [
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
            ->json('GET', route('flashcards.categories.index'));

        // THEN
        $response->assertStatus(401);
    }

    public function test__generateFlashcards_UserNotAuthorized_unauthorized(): void
    {
        // GIVEN
        $this->mockGeminiApiGenerateFlashcards();

        // WHEN
        $response = $this
            ->json('POST', route('flashcards.categories.generate-flashcards'), [
                'category_name' => 'Category',
            ]);

        // THEN
        $response->assertStatus(401);
    }

    public function test_mergeFlashcards_UserAuthorized_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_category = $this->createFlashcardCategory(['user_id' => $user->id]);
        $to_category = $this->createFlashcardCategory(['user_id' => $user->id]);

        // WHEN
        $response = $this
            ->actingAs($user)
            ->json(
                'POST',
                route('flashcards.categories.merge-flashcards', [
                    'from_category_id' => $from_category->id,
                    'to_category_id' => $to_category->id,
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
            ->json('POST', route('flashcards.categories.generate-flashcards'), [
                'category_name' => 'Category',
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
                        'word',
                        'word_lang',
                        'translation',
                        'translation_lang',
                        'context',
                        'context_translation',
                    ],
                ],
            ],
        ]);
    }
}
