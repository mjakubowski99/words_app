<?php

declare(strict_types=1);

namespace Tests\Integration\Exercise\Application\Services\FlashcardExerciseFactory;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Models\Emoji;
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
        $user_id = $this->createUser()->getId();

        $flashcard_summaries = [
            \Mockery::mock(ISessionFlashcardSummary::class)->allows([
                'getFrontWord' => 'jablko',
                'getEmoji' => Emoji::fromUnicode('🍏'),
                'getFlashcardId' => 1,
                'getBackWord' => 'apple',
                'getFrontContext' => 'context',
                'getBackContext' => 'back context',
            ]),
        ];

        $this->factory->makeExercise($flashcard_summaries, $user_id, ExerciseType::UNSCRAMBLE_WORDS);

        $this->assertDatabaseHas('exercises', [
            'exercise_type' => ExerciseType::UNSCRAMBLE_WORDS->toNumber(),
        ]);
        $this->assertDatabaseHas('unscramble_word_exercises', [
            'word' => 'apple',
            'context_sentence' => 'context',
            'word_translation' => 'jablko',
            'emoji' => $flashcard_summaries[0]->getEmoji()->toUnicode(),
        ]);
        $this->assertDatabaseHas('exercise_entries', [
            'score' => 0.0,
            'answers_count' => 0,
        ]);
    }
}
