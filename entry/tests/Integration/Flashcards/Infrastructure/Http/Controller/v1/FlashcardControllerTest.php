<?php

declare(strict_types=1);
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Shared\Utils\ValueObjects\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('store when user authorized store flashcard', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);

    // WHEN
    $response = $this->actingAs($user)
        ->json('POST', route('flashcards.store'), [
            'flashcard_category_id' => $deck->id,
            'word' => 'Word',
            'translation' => 'Translation',
            'context' => 'Context',
            'context_translation' => 'Context translation',
        ]);

    // THEN
    $response->assertStatus(204);
    $this->assertDatabaseHas('flashcards', [
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
        'front_word' => 'Word',
        'front_context' => 'Context',
        'back_word' => 'Translation',
        'back_context' => 'Context translation',
        'front_lang' => Language::pl(),
        'back_lang' => Language::en(),
    ]);
});
test('store when user not authorized fail', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();

    // WHEN
    $response = $this->actingAs($user)
        ->json('POST', route('flashcards.store'), [
            'flashcard_category_id' => $deck->id,
            'word' => 'Word',
            'translation' => 'Translation',
            'context' => 'Context',
            'context_translation' => 'Context translation',
        ]);

    // THEN
    $response->assertStatus(403);
});
test('update when user authorized update flashcard', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcard = Flashcard::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $response = $this->actingAs($user)
        ->json('PUT', route('flashcards.update', ['flashcard_id' => $flashcard->id]), [
            'flashcard_category_id' => $deck->id,
            'word' => 'Word',
            'translation' => 'Translation',
            'context' => 'Context',
            'context_translation' => 'Context translation',
        ]);

    // THEN
    $response->assertStatus(204);
    $this->assertDatabaseHas('flashcards', [
        'id' => $flashcard->id,
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
        'front_word' => 'Word',
        'front_context' => 'Context',
        'back_word' => 'Translation',
        'back_context' => 'Context translation',
        'front_lang' => Language::pl(),
        'back_lang' => Language::en(),
    ]);
});
test('update when user not authorized fail', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();
    $flashcard = Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $response = $this->actingAs($user)
        ->json('PUT', route('flashcards.update', ['flashcard_id' => $flashcard->id]), [
            'flashcard_category_id' => $deck->id,
            'word' => 'Word',
            'translation' => 'Translation',
            'context' => 'Context',
            'context_translation' => 'Context translation',
        ]);

    // THEN
    $response->assertStatus(403);
});
test('delete when user authorized delete flashcard', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcard = Flashcard::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $response = $this->actingAs($user)
        ->json('DELETE', route('flashcards.delete', ['flashcard_id' => $flashcard->id]));

    // THEN
    $response->assertStatus(204);
    $this->assertDatabaseMissing('flashcards', [
        'id' => $flashcard->id,
    ]);
});
test('delete when user not authorized fail', function () {
    // GIVEN
    $user = User::factory()->create();
    $flashcard = Flashcard::factory()->create();

    // WHEN
    $response = $this->actingAs($user)
        ->json('DELETE', route('flashcards.delete', ['flashcard_id' => $flashcard->id]));

    // THEN
    $response->assertStatus(403);
});
