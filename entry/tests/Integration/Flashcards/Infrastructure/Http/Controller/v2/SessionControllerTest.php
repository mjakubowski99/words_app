<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Infrastructure\Http\Controller\v2;

use App\Models\Exercise;
use Shared\Utils\ValueObjects\ExerciseId;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Flashcard;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use App\Models\LearningSession;
use App\Models\FlashcardPollItem;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SessionControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function store_success(): void
    {
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
    }

    /**
     * @test
     */
    public function store_scramble_word_exercise_success(): void
    {
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
    }

    /**
     * @test
     */
    public function store_word_match_exercise_success(): void
    {
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
                'session_type' => SessionType::WORD_MATCH->value,
            ]);

        // THEN
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $response->json('data.session.id'),
            'user_id' => $user->id,
            'type' => SessionType::WORD_MATCH->value,
        ]);
    }

    /**
     * @test
     */
    public function store_WhenAdminDeck_success(): void
    {
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
    }

    /**
     * @test
     */
    public function rate_success(): void
    {
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
    }

    /**
     * @test
     */
    public function rate_WhenFlashcardFromPoll(): void
    {
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
        $this->assertSame($flashcard_poll->flashcard->front_word, $response->json('data.session.next_flashcards.0.front_word'));
    }

    /**
     * @test
     */
    public function rate_WhenAdminFlashcardSuccess(): void
    {
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
        $this->assertSame($sm_two_flashcard->flashcard->front_word, $response->json()['data']['session']['next_flashcards'][0]['front_word']);
    }

    /**
     * @test
     */
    public function rate_WhenFlashcardAlreadyRated_badRequest(): void
    {
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
    }
}
