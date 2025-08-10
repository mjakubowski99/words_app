<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Application\DTO\WordMatchExerciseReadEntry;

use Exercise\Application\DTO\WordMatchExerciseReadEntry;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Tests\TestCase;

class WordMatchExerciseReadEntryTest extends TestCase
{
    /** @dataProvider sentencePartBeforeWordProvider */
    public function test__getSentencePartBeforeWord_ShouldReturnCorrectPart(string $sentence, string $word, string $expected): void
    {
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
        $this->assertSame($expected, $result);
    }

    /** @dataProvider sentencePartAfterWordProvider */
    public function test__getSentencePartAfterWord_ShouldReturnCorrectPart(string $sentence, string $word, string $expected): void
    {
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
        $this->assertSame($expected, $result);
    }

    public static function sentencePartBeforeWordProvider(): array
    {
        return [
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
        ];
    }

    public static function sentencePartAfterWordProvider(): array
    {
        return [
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
        ];
    }
}
