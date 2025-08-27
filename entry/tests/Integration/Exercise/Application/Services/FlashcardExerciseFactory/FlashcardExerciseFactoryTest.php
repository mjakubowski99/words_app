<?php

declare(strict_types=1);
use Shared\Models\Emoji;
use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummary;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Exercise\Application\Services\FlashcardExerciseFactory;

beforeEach(function () {
    $this->factory = $this->app->make(FlashcardExerciseFactory::class);
});
test('make when type is unscramble word make unscramble word exercise', function () {
    $user_id = $this->createUser()->getId();

    $flashcard_summaries = [
        Mockery::mock(ISessionFlashcardSummary::class)->allows([
            'getFrontWord' => 'jablko',
            'getEmoji' => Emoji::fromUnicode('🍏'),
            'getFlashcardId' => 1,
            'getBackWord' => 'apple',
            'getFrontContext' => 'context',
            'getBackContext' => 'back context',
            'getOrder' => 1,
        ]),
    ];
    $flashcard_summaries = Mockery::mock(ISessionFlashcardSummaries::class)
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
        'context_sentence' => 'back context',
        'word_translation' => 'jablko',
        'emoji' => $flashcard_summaries->getSummaries()[0]->getEmoji()->toUnicode(),
    ]);
    $this->assertDatabaseHas('exercise_entries', [
        'score' => 0.0,
        'answers_count' => 0,
    ]);
});
