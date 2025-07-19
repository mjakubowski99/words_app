<?php

declare(strict_types=1);

namespace Tests\Integration\Exercise\Application\Services\FlashcardExerciseFactory;

use Exercise\Application\Services\FlashcardExerciseFactory;
use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Models\Emoji;
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
        $user_id = $this->createUser()->getId();

        $flashcard_summaries = [
            \Mockery::mock(ISessionFlashcardSummary::class)->allows([
                'getFrontWord' => 'jablko',
                'getEmoji' => Emoji::fromUnicode('🍏'),
                'getFlashcardId' => 1,
                'getBackWord' => 'apple',
                'getFrontContext' => 'context',
                'getBackContext' => 'back context',
                'getOrder' => 1,
            ]),
        ];
        $flashcard_summaries = \Mockery::mock(ISessionFlashcardSummaries::class)
            ->allows([
                'getSummaries' => $flashcard_summaries,
                'getStoryId' => null,
                'hasStory' => null,
            ]);

        $this->factory->makeExercise($flashcard_summaries, $user_id, ExerciseType::UNSCRAMBLE_WORDS);

        $this->assertDatabaseHas('exercises', [
            'exercise_type' => ExerciseType::UNSCRAMBLE_WORDS->toNumber(),
        ]);
        $this->assertDatabaseHas('unscramble_word_exercises', [
            'word' => 'apple',
            'context_sentence' => 'context',
            'word_translation' => 'jablko',
            'emoji' => $flashcard_summaries->getSummaries()[0]->getEmoji()->toUnicode(),
        ]);
        $this->assertDatabaseHas('exercise_entries', [
            'score' => 0.0,
            'answers_count' => 0,
        ]);
    }
}
