<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Domain\Model\UnscrambleWordAnswer;

use Tests\TestCase;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Domain\Exceptions\ExerciseAnswerCompareFailureException;

class UnscrambleWordAnswerTest extends TestCase
{
    use DatabaseTransactions;

    private UnscrambleWordAnswer $model;

    public function test__fromString_success(): void
    {
        // GIVEN
        // WHEN
        $this->model = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'answer1');

        // THEN
        $this->assertInstanceOf(UnscrambleWordAnswer::class, $this->model);
        $this->assertSame('answer1', $this->model->toString());
    }

    public function test__compare_WhenAnswersAreTheSame_success(): void
    {
        // GIVEN
        $this->model = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'answer1');
        $answer_to_compare = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'answer1');

        // WHEN
        $assessment = $this->model->compare($answer_to_compare);

        // THEN
        $this->assertTrue($assessment->isCorrect());
        $this->assertSame(100.0, $assessment->getScore());
    }

    public function test__compare_WhenExerciseEntryIdMismatch_fail(): void
    {
        // GIVEN
        $this->model = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'test');
        $answer_to_compare = UnscrambleWordAnswer::fromString(new ExerciseEntryId(2), 'test');

        // THEN
        $this->expectException(ExerciseAnswerCompareFailureException::class);

        // WHEN
        $this->model->compare($answer_to_compare);
    }
}
