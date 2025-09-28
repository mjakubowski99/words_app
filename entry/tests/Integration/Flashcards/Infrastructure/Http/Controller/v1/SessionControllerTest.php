<?php

declare(strict_types=1);
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach()->skip('Not supported');

test('store success', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcards = Flashcard::factory(3)->create([
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson(route('flashcards.session.store'), [
            'cards_per_session' => 10,
            'category_id' => $deck->id,
            'flashcards_limit' => 5,
        ]);

    // THEN
    $response->assertStatus(200);
});
test('rate success', function () {
    // GIVEN
    $user = User::factory()->create();
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => null,
    ]);
    $flashcards = LearningSessionFlashcard::factory(4)->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson(route('flashcards.session.rate', ['session_id' => $session->id]), [
            'ratings' => [
                ['id' => $flashcards[0]->id, 'rating' => Rating::GOOD],
                ['id' => $flashcards[1]->id, 'rating' => Rating::WEAK],
            ],
        ]);

    // THEN
    $response->assertStatus(200);
});
test('rate when flashcard already rated bad request', function () {
    // GIVEN
    $user = User::factory()->create();
    $session = LearningSession::factory()->create(['user_id' => $user->id]);
    $flashcards = LearningSessionFlashcard::factory(1)->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson(route('flashcards.session.rate', ['session_id' => $session->id]), [
            'ratings' => [
                ['id' => $flashcards[0]->id, 'rating' => Rating::GOOD],
            ],
        ]);

    // THEN
    $response->assertStatus(400);
    $response->assertJsonStructure([
        'message',
        'id',
    ]);
});
