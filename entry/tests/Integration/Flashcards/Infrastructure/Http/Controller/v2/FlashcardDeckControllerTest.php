<?php

declare(strict_types=1);

use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

test('index success', function () {
    // GIVEN
    $user = $this->createUser([
        'user_language' => Language::PL,
        'learning_language' => Language::EN,
    ]);
    $deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);
    $deck = $this->createFlashcardDeck(['user_id' => $user->id]);
    $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

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
                    'owner_type',
                ],
            ],
            'page',
            'per_page',
        ],
    ]);
});
test('index admin success', function () {
    // GIVEN
    $user = $this->createUser([
        'user_language' => Language::PL,
        'learning_language' => Language::EN,
    ]);
    $admin = $this->createAdmin();
    $deck = $this->createFlashcardDeck([
        'admin_id' => $admin->id,
        'user_id' => null,
        'default_language_level' => LanguageLevel::A1,
    ]);
    $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

    // WHEN
    $response = $this->actingAs($user, 'sanctum')
        ->json('GET', route('v2.flashcards.decks.by-admins.index'), [
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
                    'owner_type',
                ],
            ],
            'page',
            'per_page',
        ],
    ]);
});
test('index admin when deck filter success', function () {
    // GIVEN
    $user = $this->createUser([
        'user_language' => Language::PL,
        'learning_language' => Language::EN,
    ]);
    $admin = $this->createAdmin();
    $deck_1 = $this->createFlashcardDeck(['admin_id' => $admin->id, 'user_id' => null, 'default_language_level' => LanguageLevel::A1]);
    $this->createFlashcard([
        'flashcard_deck_id' => $deck_1->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);
    $deck_2 = $this->createFlashcardDeck(['admin_id' => $admin->id, 'user_id' => null, 'default_language_level' => LanguageLevel::A2]);
    $this->createFlashcard([
        'flashcard_deck_id' => $deck_2->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

    // WHEN
    $response = $this->actingAs($user, 'sanctum')
        ->json('GET', route('v2.flashcards.decks.by-admins.index'), [
            'page' => 1,
            'per_page' => 15,
            'language_level' => LanguageLevel::A2,
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
                    'owner_type',
                ],
            ],
            'page',
            'per_page',
        ],
    ]);
    expect($response->json('data.decks'))->toHaveCount(1);
});
test('rating stats read when user authorized success', function () {
    // GIVEN
    $user = $this->createUser([
        'user_language' => Language::PL,
        'learning_language' => Language::EN,
    ]);
    $deck = $this->createFlashcardDeck();
    $flashcard = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
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
});

test('test polish user and german language', function () {
    // GIVEN
    $user = $this->createUser([
        'user_language' => Language::PL,
        'learning_language' => Language::IT,
    ]);

    // WHEN
    $response = $this->actingAs($user)
        ->json('POST', route('v2.flashcards.decks.generate-flashcards'), [
            'category_name' => 'Wypożyczanie samochodu',
            'language_level' => LanguageLevel::A1,
        ]);

    // THEN
    $response->assertStatus(200);
    $response->dump();
})->skip('Test uses real api(shouldnt be run on CI)');
