<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\SmTwoFlashcard;

use Tests\TestCase;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SmTwoFlashcard;

class SmTwoFlashcardTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @test
     */
    public function updateByRating_WhenRatingSmallerThanGood(
        Rating $rating,
        float $repetition_ratio,
        float $repetition_interval,
        int $repetition_count,
        float $expected_repetition_ratio,
        float $expected_repetition_interval,
        int $expected_repetition_count,
    ): void {
        // GIVEN
        $model = $this->makeSmTwoFlashcard($repetition_ratio, $repetition_interval, $repetition_count);

        // WHEN
        $model->updateByRating($rating);

        // THEN
        $this->assertSame($expected_repetition_ratio, $model->getRepetitionRatio());
        $this->assertSame($expected_repetition_interval, $model->getRepetitionInterval());
        $this->assertSame($expected_repetition_count, $model->getRepetitionCount());
    }

    public static function dataProvider(): \Generator
    {
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

        yield 'When Rating=VERY_GOOD and repetition_count=0 should set repetition interval to 1' => [
            'rating' => Rating::VERY_GOOD,
            'repetition_ratio' => 3.98,
            'repetition_interval' => 4,
            'repetition_count' => 0,
            'expected_repetition_ratio' => 4.08,
            'expected_repetition_interval' => 1.0,
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
    }

    private function makeSmTwoFlashcard(float $repetition_ratio, float $repetition_interval, int $repetition_count): SmTwoFlashcard
    {
        $user_id = new UserId(Uuid::make()->getValue());

        return new SmTwoFlashcard($user_id, new FlashcardId(1), $repetition_ratio, $repetition_interval, $repetition_count);
    }
}
