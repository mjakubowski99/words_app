<?php

declare(strict_types=1);

namespace Tests\Smoke\Flashcard;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\MainCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SessionControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test__store_ShouldCreateNewLearningSession(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $response = $this
            ->actingAs($user, 'firebase')
            ->json('POST', route('flashcards.session.store'), [
                'cards_per_session' => 10,
                'category_id' => (new MainCategory())->getId()->getValue(),
            ]);

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'session' => [
                    'id',
                    'cards_per_session',
                    'progress',
                    'is_finished',
                    'next_flashcards' => [
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
            ],
        ]);
    }

    public function test__rate_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $learning_session = LearningSession::factory()->create();
        $session_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $learning_session->id,
            'rating' => null,
        ]);
        $other_session_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $learning_session->id,
            'rating' => null,
        ]);

        // WHEN
        $response = $this
            ->actingAs($user, 'firebase')
            ->json(
                'PUT',
                route(
                    'flashcards.session.rate',
                    ['session_id' => $session_flashcard->learning_session_id]
                ),
                [
                    'ratings' => [
                        [
                            'id' => $session_flashcard->id,
                            'rating' => Rating::GOOD,
                        ],
                    ],
                ]
            );

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'session' => [
                    'id',
                    'cards_per_session',
                    'progress',
                    'is_finished',
                    'next_flashcards' => [
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
            ],
        ]);
    }
}
