<?php

declare(strict_types=1);

use Shared\Enum\LanguageLevel;
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
                    'owner_type',
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
        ->json('GET', route('v2.flashcards.decks.index'));

    // THEN
    $response->assertStatus(401);
});
test('generate flashcards user not authorized unauthorized', function () {
    // GIVEN
    $this->mockGeminiApiGenerateFlashcards();

    // WHEN
    $response = $this
        ->json('POST', route('v2.flashcards.decks.generate-flashcards'), [
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
});
test('get user authorized success', function () {
    // GIVEN
    $user = $this->createUser();
    $deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $this->createFlashcard([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user)
        ->json(
            'GET',
            route('v2.flashcards.decks.get', [
                'flashcard_deck_id' => $deck->id,
            ])
        );

    // THEN
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'id',
            'name',
            'owner_type',
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
                    'owner_type',
                ],
            ],
        ],
    ]);
});
test('generate flashcards user authorized success', function () {
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
            'owner_type',
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
                    'owner_type',
                ],
            ],
        ],
    ]);
});
test('bulk delete user authorized success', function () {
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
});
test('bulk delete user unauthorized fail', function () {
    // GIVEN
    // WHEN
    $response = $this
        ->json('DELETE', route('v2.flashcards.decks.bulk-delete'), [
            'flashcard_deck_ids' => [],
        ]);

    // THEN
    $response->assertStatus(401);
});
test('bulk delete empty decks fail', function () {
    // GIVEN
    $user = $this->createUser();

    // WHEN
    $response = $this->actingAs($user)
        ->json('DELETE', route('v2.flashcards.decks.bulk-delete'), [
            'flashcard_deck_ids' => [],
        ]);

    // THEN
    $response->assertStatus(422);
});
test('store user authorized success', function () {
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
});
test('update user authorized success', function () {
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
});
test('update user not deck owner unauthorized', function () {
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
});
