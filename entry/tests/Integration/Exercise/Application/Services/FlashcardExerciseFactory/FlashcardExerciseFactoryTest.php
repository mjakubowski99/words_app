<?php

namespace Integration\Exercise\Application\Services\FlashcardExerciseFactory;

use App\Models\LearningSessionFlashcard;
use Exercise\Application\Services\FlashcardExerciseFactory;
use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummary;
use Tests\TestCase;

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
            ])
        ];

        $exercise = $this->factory->makeExercise($flashcard_summaries, $user_id, ExerciseType::UNSCRAMBLE_WORDS);

        $this->assertSame(ExerciseType::UNSCRAMBLE_WORDS, $exercise->getExerciseType());
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->getId(),
        ]);
        $this->assertDatabaseHas('unscramble_word_exercises', [
            'exercise_id' => $exercise->getId(),
            'word' => 'apple',
            'context_sentence' => 'context',
            'word_translation' => 'jablko',
            'emoji' => '🍏',
        ]);
        $this->assertDatabaseHas('exercise_entries', [
            'exercise_id' => $exercise->getId(),
            'session_flashcard_id' => $session_flashcard_id,
            'score' => 0.0,
            'answers_count' => 0,
        ]);
    }
}
