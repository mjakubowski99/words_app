<?php

declare(strict_types=1);

namespace Integration\Exercise\Application\Services\FlashcardExerciseFactory;

use Tests\TestCase;
use Shared\Enum\ExerciseType;
use App\Models\LearningSessionFlashcard;
use Shared\Flashcard\ISessionFlashcardSummary;
use Exercise\Application\Services\FlashcardExerciseFactory;

class FlashcardExerciseFactoryTest extends TestCase
{
    private FlashcardExerciseFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->app->make(FlashcardExerciseFactory::class);
    }

    public function test__make_WhenTypeIsUnscrambleWord_makeUnscrambleWordExercise(): void
    {
        $session_flashcard_id = LearningSessionFlashcard::factory()->create()->id;
        $user_id = $this->createUser()->getId();

        $flashcard_summaries = [
            \Mockery::mock(ISessionFlashcardSummary::class)->allows([
                'getFrontWord' => 'jablko',
                'getEmoji' => '🍏',
                'getSessionFlashcardId' => $session_flashcard_id,
                'getBackWord' => 'apple',
                'getFrontContext' => 'context',
                'getBackContext' => 'back context',
            ]),
        ];

        $this->factory->makeExercise($flashcard_summaries, $user_id, ExerciseType::UNSCRAMBLE_WORDS);

        $this->assertDatabaseHas('exercises', [
            'exercise_type' => ExerciseType::UNSCRAMBLE_WORDS->value,
        ]);
        $this->assertDatabaseHas('unscramble_word_exercises', [
            'word' => 'apple',
            'context_sentence' => 'context',
            'word_translation' => 'jablko',
            'emoji' => '🍏',
        ]);
        $this->assertDatabaseHas('exercise_entries', [
            'session_flashcard_id' => $session_flashcard_id,
            'score' => 0.0,
            'answers_count' => 0,
        ]);
    }
}
