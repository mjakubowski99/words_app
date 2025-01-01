<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\RateableSessionFlashcards;

use Tests\TestCase;
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Domain\Exceptions\SessionFinishedException;
use Flashcard\Domain\Exceptions\RateableSessionFlashcardNotFound;
use Flashcard\Domain\Exceptions\SessionFlashcardAlreadyRatedException;

class RateableSessionFlashcardsTest extends TestCase
{
    private RateableSessionFlashcards $model;

    public function test__construct_SessionFinished_fail(): void
    {
        // GIVEN

        // THEN
        $this->expectException(SessionFinishedException::class);

        // WHEN
        $this->model = new RateableSessionFlashcards(
            new SessionId(1),
            UserId::fromString(Uuid::make()->getValue()),
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
            UserId::fromString(Uuid::make()->getValue()),
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

    public function test__rate_RateNotExistingId_fail(): void
    {
        // GIVEN
        $expected_id = new SessionFlashcardId(5);

        $this->model = new RateableSessionFlashcards(
            new SessionId(1),
            UserId::fromString(Uuid::make()->getValue()),
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

        // THEN
        $this->expectException(RateableSessionFlashcardNotFound::class);

        // WHEN
        $this->model->rate(new SessionFlashcardId(123123213132), Rating::GOOD);
    }

    public function test__rate_WhenFlashcardAlreadyRated_fail(): void
    {
        // GIVEN
        $expected_id = new SessionFlashcardId(5);

        $this->model = new RateableSessionFlashcards(
            new SessionId(1),
            UserId::fromString(Uuid::make()->getValue()),
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
        $this->model->rate($expected_id, Rating::GOOD);

        // THEN
        $this->expectException(SessionFlashcardAlreadyRatedException::class);

        // WHEN
        $this->model->rate($expected_id, Rating::WEAK);
    }
}
