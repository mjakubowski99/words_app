<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use App\Models\SmTwoFlashcard;
use App\Models\User;
use Flashcard\Application\Command\FlashcardRating;
use Flashcard\Application\Command\RateFlashcards;
use Flashcard\Application\Command\RateFlashcardsCommand;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RateFlashcardsTest extends TestCase
{
    use DatabaseTransactions;

    private RateFlashcards $command_handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command_handler = $this->app->make(RateFlashcards::class);
    }

    /**
     * @test
     */
    public function handle_ShouldSaveFlashcardRatings(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $session = LearningSession::factory()->create([
            'user_id' => $user->id,
        ]);
        $session_flashcards = [
            LearningSessionFlashcard::factory()->create(['learning_session_id' => $session->id, 'rating' => null]),
            LearningSessionFlashcard::factory()->create(['learning_session_id' => $session->id, 'rating' => null])
        ];
        $sm_two_flashcards = [
            SmTwoFlashcard::factory()->create([
                'user_id' => $user->id,
                'flashcard_id' => $session_flashcards[0]->flashcard_id,
                'repetition_interval' => 1,
            ]),
            SmTwoFlashcard::factory()->create([
                'user_id' => $user->id,
                'flashcard_id' => $session_flashcards[1]->flashcard_id,
                'repetition_interval' => 1,
            ])
        ];
        $command = new RateFlashcardsCommand(
            $user->getId(),
            $session->getId(),
            [
                new FlashcardRating($session_flashcards[0]->getId(), Rating::GOOD),
                new FlashcardRating($session_flashcards[1]->getId(), Rating::VERY_GOOD),
            ]
        );

        // WHEN
        $this->command_handler->handle($command);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcards[0]->id,
            'rating' => Rating::GOOD,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcards[1]->id,
            'rating' => Rating::VERY_GOOD,
        ]);
    }
}
