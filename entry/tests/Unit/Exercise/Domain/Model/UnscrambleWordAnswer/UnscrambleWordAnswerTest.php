<?php

declare(strict_types=1);
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Domain\Exceptions\ExerciseAnswerCompareFailureException;

uses(DatabaseTransactions::class);

test('from string success', function () {
    // GIVEN
    // WHEN
    $this->model = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'answer1');

    // THEN
    expect($this->model)->toBeInstanceOf(UnscrambleWordAnswer::class);
    expect($this->model->toString())->toBe('answer1');
});
test('compare when answers are the same success', function () {
    // GIVEN
    $this->model = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'answer1');
    $answer_to_compare = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'answer1');

    // WHEN
    $assessment = $this->model->compare($answer_to_compare);

    // THEN
    expect($assessment->isCorrect())->toBeTrue();
    expect($assessment->getRealScore())->toBe(100.0);
});
test('compare when exercise entry id mismatch fail', function () {
    // GIVEN
    $this->model = UnscrambleWordAnswer::fromString(new ExerciseEntryId(1), 'test');
    $answer_to_compare = UnscrambleWordAnswer::fromString(new ExerciseEntryId(2), 'test');

    // THEN
    $this->expectException(ExerciseAnswerCompareFailureException::class);

    // WHEN
    $this->model->compare($answer_to_compare);
});
