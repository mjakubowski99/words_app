<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Infrastructure\Http\Controller;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
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
        $category = FlashcardCategory::factory()->create([
            'user_id' => $user->id,
        ]);
        $flashcards = Flashcard::factory(3)->create([
            'flashcard_category_id' => $category->id,
        ]);

        // WHEN
        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(route('flashcards.session.store'), [
                'cards_per_session' => 10,
                'category_id' => $category->id,
                'flashcards_limit' => 5,
            ]);

        // THEN
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function rate_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $session = LearningSession::factory()->create();
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
    }

    /**
     * @test
     */
    public function rate_WhenFlashcardAlreadyRated_badRequest(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $session = LearningSession::factory()->create();
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
    }
}
