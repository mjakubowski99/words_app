<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\RateableSessionFlashcards;

use Flashcard\Domain\Exceptions\SessionFinishedException;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\Uuid;
use Tests\TestCase;

class RateableSessionFlashcardsTest extends TestCase
{
    private RateableSessionFlashcards $model;

    public function test__construct_SessionFinished_fail(): void
    {
        // GIVEN

        // THEN
        $this->expectException(SessionFinishedException::class);

        //WHEN
        $this->model = new RateableSessionFlashcards(
            new SessionId(1),
            new Owner(new OwnerId(Uuid::make()->getValue()), FlashcardOwnerType::USER),
            SessionStatus::FINISHED,
            9,
            10,
            []
        );
    }

    public function test__rate_ShouldSetRatingForCorrectFlashcard(): void
    {
        // GIVEN
        $expected_id = new SessionFlashcardId(5);

        $this->model = new RateableSessionFlashcards(
            new SessionId(1),
            new Owner(new OwnerId(Uuid::make()->getValue()), FlashcardOwnerType::USER),
            SessionStatus::STARTED,
            9,
            10,
            [
                new RateableSessionFlashcard(
                    new SessionFlashcardId(4),
                    new FlashcardId(2),
                ),
                new RateableSessionFlashcard(
                    $expected_id,
                    new FlashcardId(2),
                ),
            ]
        );

        // WHEN
        $this->model->rate($expected_id, Rating::GOOD);

        // THEN
        $this->assertSame(Rating::GOOD, $this->model->getRateableSessionFlashcards()[1]->getRating());
    }
}
