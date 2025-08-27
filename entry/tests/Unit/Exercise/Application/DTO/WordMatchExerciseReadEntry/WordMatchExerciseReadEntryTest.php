<?php

declare(strict_types=1);

use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Application\DTO\WordMatchExerciseReadEntry;

test('get sentence part before word should return correct part', function (string $sentence, string $word, string $expected) {
    // GIVEN
    $entry = new WordMatchExerciseReadEntry(
        new ExerciseEntryId(1),
        $word,
        'translation',
        $sentence
    );

    // WHEN
    $result = $entry->getSentencePartBeforeWord();

    // THEN
    expect($result)->toBe($expected);
})->with('sentencePartBeforeWordProvider');

test('get sentence part after word should return correct part', function (string $sentence, string $word, string $expected) {
    // GIVEN
    $entry = new WordMatchExerciseReadEntry(
        new ExerciseEntryId(1),
        $word,
        'translation',
        $sentence
    );

    // WHEN
    $result = $entry->getSentencePartAfterWord();

    // THEN
    expect($result)->toBe($expected);
})->with('sentencePartAfterWordProvider');

dataset('sentencePartAfterWordProvider', [
    'word at start' => [
        'sentence' => 'cat is running',
        'word' => 'cat',
        'expected' => ' is running',
    ],
    'word in middle' => [
        'sentence' => 'The cat is running',
        'word' => 'cat',
        'expected' => ' is running',
    ],
    'word at end' => [
        'sentence' => 'I see a cat',
        'word' => 'cat',
        'expected' => '',
    ],
    'word at end with dot' => [
        'sentence' => 'I see a cat.',
        'word' => 'cat',
        'expected' => '.',
    ],
    'word with special chars' => [
        'sentence' => 'The cat-dog is here',
        'word' => 'cat-dog',
        'expected' => ' is here',
    ],
    'sentence without word' => [
        'sentence' => 'The dog is here',
        'word' => 'cat',
        'expected' => '',
    ],
    'double word' => [
        'sentence' => 'The dog is here with another dog',
        'word' => 'dog',
        'expected' => ' is here with another dog',
    ],
]);

dataset('sentencePartBeforeWordProvider', [
    'word at start' => [
        'sentence' => 'cat is running',
        'word' => 'cat',
        'expected' => '',
    ],
    'word in middle' => [
        'sentence' => 'The cat is running',
        'word' => 'cat',
        'expected' => 'The ',
    ],
    'word at end' => [
        'sentence' => 'I see a cat',
        'word' => 'cat',
        'expected' => 'I see a ',
    ],
    'word at end with dot' => [
        'sentence' => 'I see a cat.',
        'word' => 'cat',
        'expected' => 'I see a ',
    ],
    'word with special chars' => [
        'sentence' => 'The cat-dog is here',
        'word' => 'cat-dog',
        'expected' => 'The ',
    ],
    'sentence without word' => [
        'sentence' => 'The dog is here',
        'word' => 'cat',
        'expected' => 'The dog is here',
    ],
    'double word' => [
        'sentence' => 'The dog is here with another dog',
        'word' => 'dog',
        'expected' => 'The ',
    ],
]);
