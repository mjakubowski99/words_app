<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\SmTwoFlashcard;

use Tests\TestCase;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\Uuid;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;

class SmTwoFlashcardTest extends TestCase
{
    use SmTwoFlashcardTrait;

    /**
     * @dataProvider dataProvider
     */
    public function test__updateByRating_WhenRatingSmallerThanGood(
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
}
