<?php

declare(strict_types=1);

use Exercise\Domain\Models\Exercise\UnscrambleWordsExercise;
use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\UserId;

test('new exercise should create exercise with scrambled word', function () {
    // GIVEN
    // WHEN
    $exercise = UnscrambleWordsExercise::newExercise(
        UserId::new(),
        'word',
        'context sentence',
        'word translation',
        'context sentence translation',
        Emoji::fromUnicode(';)'),
    );

    // THEN
    $this->assertNotSame($exercise->getWord(), $exercise->getScrambledWord());

    $sorted_word = mb_str_split($exercise->getWord());
    sort($sorted_word);

    $sorted_scrambled_word = mb_str_split($exercise->getScrambledWord());
    sort($sorted_scrambled_word);

    expect(json_encode($sorted_scrambled_word))->toBe(json_encode($sorted_word));
});
test('new exercise should create only one exercise entry with correct properties', function () {
    // GIVEN
    // WHEN
    $exercise = UnscrambleWordsExercise::newExercise(
        UserId::new(),
        'word',
        'context sentence',
        'word translation',
        'context sentence translation',
        Emoji::fromUnicode(';)'),
    );

    // THEN
    expect($exercise->getExerciseEntries())->toHaveCount(1)
        ->and($exercise->getExerciseEntries()[0]->getScore())->toBe(0.0)
        ->and($exercise->getExerciseEntries()[0]->getLastUserAnswer())->toBeNull()
        ->and($exercise->getExerciseEntries()[0]->getCorrectAnswer()->toString())->toBe('word');
});
