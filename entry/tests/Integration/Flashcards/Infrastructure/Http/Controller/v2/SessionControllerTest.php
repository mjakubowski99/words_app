<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Admin;
use App\Models\Story;
use App\Models\Flashcard;
use Shared\Enum\Language;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use App\Models\StoryFlashcard;
use App\Models\LearningSession;
use App\Models\FlashcardPollItem;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

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
        ->postJson(route('v2.flashcards.session.store'), [
            'cards_per_session' => 10,
            'flashcard_deck_id' => $deck->id,
            'flashcards_limit' => 5,
        ]);

    // THEN
    $response->assertStatus(200);
});

test('store in user language', function () {
    // GIVEN
    $user = User::factory()->create([
        'user_language' => Language::ES,
        'learning_language' => Language::PL,
    ]);
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::ES,
    ]);
    $expected_flashcard = Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::ES,
        'back_lang' => Language::PL,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson(route('v2.flashcards.session.store'), [
            'cards_per_session' => 10,
            'flashcard_deck_id' => $deck->id,
            'flashcards_limit' => 5,
        ]);

    // THEN
    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data.session.next_flashcards');

    expect($response->json('data.session.next_flashcards.0.front_lang'))
        ->toBe($user->user_language->value)
        ->and($response->json('data.session.next_flashcards.0.back_lang'))
        ->toBe($user->learning_language->value);
});

test('store unscramble word exercise success', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcards = Flashcard::factory(3)->create([
        'flashcard_deck_id' => $deck->id,
        'emoji' => null,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson(route('v2.flashcards.session.store'), [
            'cards_per_session' => 10,
            'flashcard_deck_id' => $deck->id,
            'session_type' => SessionType::UNSCRAMBLE_WORDS->value,
        ]);

    // THEN
    $response->assertStatus(200);

    $this->assertDatabaseHas('learning_sessions', [
        'id' => $response->json('data.session.id'),
        'user_id' => $user->id,
        'type' => SessionType::UNSCRAMBLE_WORDS->value,
    ]);
});

test('store unscramble word exercise in correct language', function () {
    // GIVEN
    $user = User::factory()->create([
        'user_language' => Language::IT,
        'learning_language' => Language::FR,
    ]);
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::DE,
        'back_lang' => Language::ZH,
    ]);
    $expected_flashcard = Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::IT,
        'back_lang' => Language::FR,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson(route('v2.flashcards.session.store'), [
            'cards_per_session' => 10,
            'flashcard_deck_id' => $deck->id,
            'session_type' => SessionType::UNSCRAMBLE_WORDS->value,
        ]);

    // THEN
    $response->assertStatus(200);
    $this->assertSame($expected_flashcard->front_word, $response->json('data.session.next_exercises.0.data.front_word'));
});

test('store word match exercise in correct language', function () {
    // GIVEN
    Flashcard::query()->forceDelete();
    $user = User::factory()->create([
        'user_language' => Language::IT,
        'learning_language' => Language::FR,
    ]);
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::DE,
        'back_lang' => Language::ZH,
    ]);
    $expected_flashcards = Flashcard::factory(4)->create([
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::IT,
        'back_lang' => Language::FR,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson(route('v2.flashcards.session.store'), [
            'cards_per_session' => 10,
            'flashcard_deck_id' => $deck->id,
            'session_type' => SessionType::WORD_MATCH->value,
        ]);

    // THEN
    $response->assertStatus(200);

    $answer_options = $response->json('data.session.next_exercises.0.data.answer_options');
    $flashcards = $expected_flashcards->pluck('back_word')->toArray();

    sort($answer_options);
    sort($flashcards);

    expect(json_encode($answer_options))
        ->toBe(json_encode($flashcards));
});

test('store when admin deck success', function () {
    // GIVEN
    $user = $this->createUser();
    $admin = Admin::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => null,
        'admin_id' => $admin->id,
    ]);
    $flashcards = Flashcard::factory(3)->create([
        'flashcard_deck_id' => $deck->id,
        'admin_id' => $admin->id,
        'user_id' => null,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->postJson(route('v2.flashcards.session.store'), [
            'cards_per_session' => 10,
            'flashcard_deck_id' => $deck->id,
            'flashcards_limit' => 5,
        ]);

    // THEN
    $response->assertStatus(200);
    $this->assertDatabaseHas('learning_sessions', [
        'user_id' => $user->id,
    ]);
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
        ->putJson(route('v2.flashcards.session.rate', ['session_id' => $session->id]), [
            'ratings' => [
                ['id' => $flashcards[0]->id, 'rating' => Rating::GOOD],
                ['id' => $flashcards[1]->id, 'rating' => Rating::WEAK],
            ],
        ]);

    // THEN
    $response->assertStatus(200);
});

test('rate when flashcard from poll', function () {
    // GIVEN
    $user = User::factory()->create();
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => null,
    ]);
    $flashcard_poll = FlashcardPollItem::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcard = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson(route('v2.flashcards.session.rate', ['session_id' => $session->id]), [
            'ratings' => [
                ['id' => $flashcard->id, 'rating' => Rating::GOOD],
            ],
        ]);

    // THEN
    $response->assertStatus(200);
    expect($response->json('data.session.next_flashcards.0.front_word'))->toBe($flashcard_poll->flashcard->front_word);
});

test('rate when admin flashcard success', function () {
    // GIVEN
    Flashcard::query()->forceDelete();
    $expected_flashcard_front_word = 'adssdaasd';
    $user = User::factory()->create();
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => null,
        'cards_per_session' => 10,
    ]);
    $flashcard = Flashcard::factory()->create([
        'user_id' => null,
        'admin_id' => Admin::factory()->create()->id,
        'front_word' => $expected_flashcard_front_word,
    ]);
    $session_flashcard = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
        'flashcard_id' => $flashcard->id,
    ]);
    $sm_two_flashcard = SmTwoFlashcard::factory()->create([
        'user_id' => $user->id,
        'flashcard_id' => $flashcard->id,
        'repetitions_in_session' => 0,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson(route('v2.flashcards.session.rate', ['session_id' => $session->id]), [
            'ratings' => [
                ['id' => $session_flashcard->id, 'rating' => Rating::GOOD],
            ],
        ]);

    // THEN
    $response->assertStatus(200);
    expect($response->json()['data']['session']['next_flashcards'][0]['front_word'])->toBe($sm_two_flashcard->flashcard->front_word);
});

test('rate when flashcard already rated bad request', function () {
    // GIVEN
    $user = User::factory()->create();
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
    ]);
    $flashcards = LearningSessionFlashcard::factory(1)->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD,
    ]);

    // WHEN
    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson(route('v2.flashcards.session.rate', ['session_id' => $session->id]), [
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
