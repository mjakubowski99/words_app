<?php

declare(strict_types=1);
use Tests\Traits\GeminiApiFaker;
use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

uses(GeminiApiFaker::class);

test('index when user authorized success', function () {
    // GIVEN
    $user = $this->createUser();
    $this->createFlashcardDeck(['user_id' => $user->id]);

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
});
test('index when user unauthorized fail', function () {
    // GIVEN
    $user = $this->createUser();

    // WHEN
    $response = $this
        ->json('GET', route('flashcards.categories.index'));

    // THEN
    $response->assertStatus(401);
});
test('generate flashcards user not authorized unauthorized', function () {
    // GIVEN
    $this->mockGeminiApiGenerateFlashcards();

    // WHEN
    $response = $this
        ->json('POST', route('flashcards.categories.generate-flashcards'), [
            'category_name' => 'Category',
        ]);

    // THEN
    $response->assertStatus(401);
});
test('merge flashcards user authorized success', function () {
    // GIVEN
    $user = $this->createUser();
    $from_deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $to_deck = $this->createFlashcardDeck(['user_id' => $user->id]);

    // WHEN
    $response = $this
        ->actingAs($user)
        ->json(
            'POST',
            route('flashcards.categories.merge-flashcards', [
                'from_category_id' => $from_deck->id,
                'to_category_id' => $to_deck->id,
            ]),
            [
                'new_name' => 'New name',
            ]
        );

    // THEN
    $response->assertStatus(204);
});
test('generate flashcards user authorized success', function () {
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
});
