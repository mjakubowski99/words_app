<?php

declare(strict_types=1);

use Exercise\Domain\Exceptions\ExerciseStatusTransitionException;
use Exercise\Domain\Models\Answer\Answer;
use Exercise\Domain\Models\AnswerAssessment;
use Exercise\Domain\Models\ExerciseEntry\ExerciseEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Shared\Enum\ExerciseType;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\UserId;
use Tests\Unit\Exercise\Domain\Model\Exercise\ConcreteTestExercise;

test('assesses answer status allows for assessment and entry exists', function () {
    // GIVEN
    $entry_id = new ExerciseEntryId(1);
    $answer_assessment = Mockery::mock(AnswerAssessment::class)->allows([
        'getRealScore' => 0.5,
        'isCorrect' => false,
    ]);
    $answer = Mockery::mock(Answer::class)->allows([
        'getExerciseEntryId' => $entry_id,
        'compare' => $answer_assessment,
    ]);
    $correct_answer = Mockery::mock(Answer::class)->allows([
        'compare' => $answer_assessment,
    ]);

    $entry = Mockery::mock(ExerciseEntry::class)->allows([
        'getId' => $entry_id,
        'isUpdated' => true,
        'getCorrectAnswer' => $correct_answer,
        'isLastAnswerCorrect' => true,
        'setLastUserAnswer' => Mockery::on(function ($arg) use ($answer) {
            return $arg === $answer;
        }),
        'getOrder' => 0,
    ]);
    $other_entry = Mockery::mock(ExerciseEntry::class)->allows([
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
    expect($result->isCorrect())->toBe($answer_assessment->isCorrect());
    expect($result->getRealScore())->toBe($answer_assessment->getRealScore());
});

test('get updated entries return only entries with updated status set to true', function () {
    // GIVEN
    $updated_entry = Mockery::mock(ExerciseEntry::class)->allows([
        'getId' => new ExerciseEntryId(1),
        'isUpdated' => true,
    ]);
    $other_entry = Mockery::mock(ExerciseEntry::class)->allows([
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
    expect($entries)->toHaveCount(1);
    expect($entries[0])->toBeInstanceOf(ExerciseEntry::class);
    expect($entries[0]->getId())->toBe($updated_entry->getId());
});

test('set status when transition allowed success', function (ExerciseStatus $current_status, ExerciseStatus $new_status) {
    // GIVEN
    $this->model = ConcreteTestExercise::new($current_status);

    // WHEN
    $this->model->setStatus($new_status);

    // THEN
    expect($this->model->getStatus())->toBe($new_status);
})->with('statusTransitionProvider');

test('set status when transition not allowed fail', function (ExerciseStatus $current_status, ExerciseStatus $new_status) {
    // GIVEN
    $this->model = ConcreteTestExercise::new($current_status);

    // THEN
    $this->expectException(ExerciseStatusTransitionException::class);

    // WHEN
    $this->model->setStatus($new_status);
})->with('notAllowedTransitionProvider');

test('skip exercise sets status to skipped', function () {
    // GIVEN
    $this->model = ConcreteTestExercise::new(ExerciseStatus::NEW);

    // WHEN
    $this->model->skipExercise();

    // THEN
    expect($this->model->getStatus())->toBe(ExerciseStatus::SKIPPED);
});

test('mark as finished sets status to done', function () {
    // GIVEN
    $this->model = ConcreteTestExercise::new(ExerciseStatus::IN_PROGRESS);

    // WHEN
    $this->model->markAsFinished();

    // THEN
    expect($this->model->getStatus())->toBe(ExerciseStatus::DONE);
});

dataset('notAllowedTransitionProvider', function () {
    yield 'done_to_skipped' => [ExerciseStatus::DONE, ExerciseStatus::SKIPPED];

    yield 'skipped_to_done' => [ExerciseStatus::SKIPPED, ExerciseStatus::DONE];

    yield 'done_to_in_progress' => [ExerciseStatus::DONE, ExerciseStatus::IN_PROGRESS];

    yield 'done_to_new' => [ExerciseStatus::DONE, ExerciseStatus::NEW];

    yield 'skipped_to_in_progress' => [ExerciseStatus::SKIPPED, ExerciseStatus::IN_PROGRESS];

    yield 'skipped_to_new' => [ExerciseStatus::SKIPPED, ExerciseStatus::NEW];
});

dataset('statusTransitionProvider', function () {
    yield 'new_to_in_progress' => [ExerciseStatus::NEW, ExerciseStatus::IN_PROGRESS];

    yield 'in_progress_to_done' => [ExerciseStatus::IN_PROGRESS, ExerciseStatus::DONE];

    yield 'in_progress_to_skipped' => [ExerciseStatus::IN_PROGRESS, ExerciseStatus::SKIPPED];

    yield 'new_to_done' => [ExerciseStatus::NEW, ExerciseStatus::DONE];

    yield 'new_to_skipped' => [ExerciseStatus::NEW, ExerciseStatus::SKIPPED];
});
