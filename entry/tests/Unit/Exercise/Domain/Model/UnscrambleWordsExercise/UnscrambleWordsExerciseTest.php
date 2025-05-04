<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Domain\Model\UnscrambleWordsExercise;

use Tests\TestCase;
use Shared\Utils\ValueObjects\UserId;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\SessionFlashcardId;

class UnscrambleWordsExerciseTest extends TestCase
{
    public function test__newExercise_ShouldCreateExerciseWithScrambledWord(): void
    {
        // GIVEN
        // WHEN
        $exercise = UnscrambleWordsExercise::newExercise(
            UserId::new(),
            new SessionFlashcardId(1),
            'word',
            'context sentence',
            'word translation',
            ';)',
        );

        // THEN
        $this->assertNotSame($exercise->getWord(), $exercise->getScrambledWord());

        $sorted_word = mb_str_split($exercise->getWord());
        sort($sorted_word);

        $sorted_scrambled_word = mb_str_split($exercise->getScrambledWord());
        sort($sorted_scrambled_word);

        $this->assertSame(json_encode($sorted_word), json_encode($sorted_scrambled_word));
    }

    public function test__newExercise_ShouldCreateOnlyOneExerciseEntryWithCorrectProperties(): void
    {
        // GIVEN
        // WHEN
        $exercise = UnscrambleWordsExercise::newExercise(
            UserId::new(),
            new SessionFlashcardId(1),
            'word',
            'context sentence',
            'word translation',
            ';)',
        );

        // THEN
        $this->assertCount(1, $exercise->getExerciseEntries());
        $this->assertSame(0.0, $exercise->getExerciseEntries()[0]->getScore());
        $this->assertNull($exercise->getExerciseEntries()[0]->getLastUserAnswer());
        $this->assertSame('word', $exercise->getExerciseEntries()[0]->getCorrectAnswer()->toString());
    }
}
