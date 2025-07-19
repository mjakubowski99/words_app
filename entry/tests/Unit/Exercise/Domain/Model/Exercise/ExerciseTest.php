<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Domain\Model\Exercise;

use Tests\TestCase;
use Shared\Enum\ExerciseType;
use Exercise\Domain\Models\Answer;
use Exercise\Domain\Models\Exercise;
use Shared\Utils\ValueObjects\UserId;
use Exercise\Domain\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\AnswerAssessment;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Exceptions\ExerciseStatusTransitionException;

class ExerciseTest extends TestCase
{
    use ExerciseTrait;

    private Exercise $model;

    public function test__assessesAnswer_statusAllowsForAssessmentAndEntryExists(): void
    {
        // GIVEN
        $entry_id = new ExerciseEntryId(1);
        $answer_assessment = \Mockery::mock(AnswerAssessment::class)->allows([
            'getScore' => 0.5,
            'isCorrect' => false,
        ]);
        $answer = \Mockery::mock(Answer::class)->allows([
            'getExerciseEntryId' => $entry_id,
            'compare' => $answer_assessment,
        ]);
        $correct_answer = \Mockery::mock(Answer::class)->allows([
            'compare' => $answer_assessment,
        ]);

        $entry = \Mockery::mock(ExerciseEntry::class)->allows([
            'getId' => $entry_id,
            'isUpdated' => true,
            'getCorrectAnswer' => $correct_answer,
            'isLastAnswerCorrect' => true,
            'setLastUserAnswer' => \Mockery::on(function ($arg) use ($answer) {
                return $arg === $answer;
            }),
            'getOrder' => 0,
        ]);
        $other_entry = \Mockery::mock(ExerciseEntry::class)->allows([
            'getId' => new ExerciseEntryId(2),
            'isUpdated' => false,
            'isLastAnswerCorrect' => false,
            'getOrder' => 1,
        ]);

        $this->model = new ConcreteTestExercise(
            new ExerciseId(1),
            UserId::new(),
            [$other_entry, $entry],
            ExerciseStatus::IN_PROGRESS,
            ExerciseType::UNSCRAMBLE_WORDS
        );

        // WHEN
        $result = $this->model->assessAnswer($answer);

        // THEN
        $this->assertSame($answer_assessment->isCorrect(), $result->isCorrect());
        $this->assertSame($answer_assessment->getScore(), $result->getScore());
    }

    public function test__getUpdatedEntries_returnOnlyEntriesWithUpdatedStatusSetToTrue(): void
    {
        // GIVEN
        $updated_entry = \Mockery::mock(ExerciseEntry::class)->allows([
            'getId' => new ExerciseEntryId(1),
            'isUpdated' => true,
        ]);
        $other_entry = \Mockery::mock(ExerciseEntry::class)->allows([
            'getId' => new ExerciseEntryId(2),
            'isUpdated' => false,
        ]);

        $this->model = new ConcreteTestExercise(
            new ExerciseId(1),
            UserId::new(),
            [$updated_entry, $other_entry],
            ExerciseStatus::IN_PROGRESS,
            ExerciseType::UNSCRAMBLE_WORDS
        );

        // WHEN
        $entries = $this->model->getUpdatedEntries();

        // THEN
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(ExerciseEntry::class, $entries[0]);
        $this->assertSame($updated_entry->getId(), $entries[0]->getId());
    }

    /**
     * @dataProvider statusTransitionProvider
     */
    public function test__setStatus_WhenTransitionAllowed_success(ExerciseStatus $current_status, ExerciseStatus $new_status): void
    {
        // GIVEN
        $this->model = ConcreteTestExercise::new($current_status);

        // WHEN
        $this->model->setStatus($new_status);

        // THEN
        $this->assertSame($new_status, $this->model->getStatus());
    }

    /**
     * @dataProvider notAllowedTransitionProvider
     */
    public function test__setStatus_WhenTransitionNotAllowed_fail(ExerciseStatus $current_status, ExerciseStatus $new_status): void
    {
        // GIVEN
        $this->model = ConcreteTestExercise::new($current_status);

        // THEN
        $this->expectException(ExerciseStatusTransitionException::class);

        // WHEN
        $this->model->setStatus($new_status);
    }

    public function test__skipExercise_setsStatusToSkipped(): void
    {
        // GIVEN
        $this->model = ConcreteTestExercise::new(ExerciseStatus::NEW);

        // WHEN
        $this->model->skipExercise();

        // THEN
        $this->assertSame(ExerciseStatus::SKIPPED, $this->model->getStatus());
    }

    public function test__markAsFinished_setsStatusToDone(): void
    {
        // GIVEN
        $this->model = ConcreteTestExercise::new(ExerciseStatus::IN_PROGRESS);

        // WHEN
        $this->model->markAsFinished();

        // THEN
        $this->assertSame(ExerciseStatus::DONE, $this->model->getStatus());
    }
}
