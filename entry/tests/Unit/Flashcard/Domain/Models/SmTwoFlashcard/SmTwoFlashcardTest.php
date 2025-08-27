<?php

declare(strict_types=1);

use Flashcard\Domain\Models\Rating;
use Tests\Unit\Flashcard\Domain\Models\SmTwoFlashcard\SmTwoFlashcardTrait;

uses(SmTwoFlashcardTrait::class);

test('update by rating when rating smaller than good', function (Rating $rating, float $repetition_ratio, float $repetition_interval, int $repetition_count, float $expected_repetition_ratio, float $expected_repetition_interval, int $expected_repetition_count) {
    // GIVEN
    $model = $this->makeSmTwoFlashcard($repetition_ratio, $repetition_interval, $repetition_count);

    // WHEN
    $model->updateByRating($rating);

    // THEN
    expect($model->getRepetitionRatio())->toBe($expected_repetition_ratio)
        ->and($model->getRepetitionInterval())->toBe($expected_repetition_interval)
        ->and($model->getRepetitionCount())->toBe($expected_repetition_count);
})->with('dataProvider');

dataset('dataProvider', function () {
    yield 'When Rating=WEAK should reset counter and set interval to 1' => [
        'rating' => Rating::WEAK,
        'repetition_ratio' => 2.5,
        'repetition_interval' => 2,
        'repetition_count' => 1,
        'expected_repetition_ratio' => 2.36,
        'expected_repetition_interval' => 1.0,
        'expected_repetition_count' => 0,
    ];

    yield 'When Rating=UNKNOWN should reset counter and set interval to 1' => [
        'rating' => Rating::WEAK,
        'repetition_ratio' => 3,
        'repetition_interval' => 3,
        'repetition_count' => 4,
        'expected_repetition_ratio' => 2.86,
        'expected_repetition_interval' => 1.0,
        'expected_repetition_count' => 0,
    ];

    yield 'When Rating=GOOD and repetition_count=0 should set repetition interval to 1' => [
        'rating' => Rating::GOOD,
        'repetition_ratio' => 3.98,
        'repetition_interval' => 4,
        'repetition_count' => 0,
        'expected_repetition_ratio' => 3.98,
        'expected_repetition_interval' => 1.0,
        'expected_repetition_count' => 1,
    ];

    yield 'When Rating=VERY_GOOD and repetition_count=0 should set repetition interval to 6' => [
        'rating' => Rating::VERY_GOOD,
        'repetition_ratio' => 3.98,
        'repetition_interval' => 4,
        'repetition_count' => 0,
        'expected_repetition_ratio' => 4.08,
        'expected_repetition_interval' => 6.0,
        'expected_repetition_count' => 1,
    ];

    yield 'When Rating=VERY_GOOD and repetition_count=1 should set repetition interval to 6' => [
        'rating' => Rating::VERY_GOOD,
        'repetition_ratio' => 3.22,
        'repetition_interval' => 4,
        'repetition_count' => 1,
        'expected_repetition_ratio' => 3.32,
        'expected_repetition_interval' => 6.0,
        'expected_repetition_count' => 2,
    ];

    yield 'When Rating=GOOD and repetition_count greater than 1 should set repetition interval to previous value x repetition ratio' => [
        'rating' => Rating::GOOD,
        'repetition_ratio' => 4.223,
        'repetition_interval' => 5,
        'repetition_count' => 10,
        'expected_repetition_ratio' => 4.223,
        'expected_repetition_interval' => 21.115,
        'expected_repetition_count' => 11,
    ];

    yield 'When Rating=VERY_GOOD and repetition_count greater than 1 should set repetition interval to previous value x repetition ratio' => [
        'rating' => Rating::VERY_GOOD,
        'repetition_ratio' => 3.1,
        'repetition_interval' => 2,
        'repetition_count' => 3,
        'expected_repetition_ratio' => 3.2,
        'expected_repetition_interval' => 6.2,
        'expected_repetition_count' => 4,
    ];
});
