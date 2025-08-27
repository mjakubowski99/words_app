<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\SmTwoFlashcard;

use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;

trait SmTwoFlashcardTrait
{
    public function makeSmTwoFlashcard(float $repetition_ratio, float $repetition_interval, int $repetition_count): SmTwoFlashcard
    {
        return new SmTwoFlashcard(
            UserId::fromString(Uuid::make()->getValue()),
            new FlashcardId(1),
            $repetition_ratio,
            $repetition_interval,
            $repetition_count
        );
    }
}
