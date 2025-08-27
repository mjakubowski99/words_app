<?php

declare(strict_types=1);
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Flashcard\Domain\Exceptions\SessionFinishedException;
use Flashcard\Domain\Exceptions\RateableSessionFlashcardNotFound;
use Flashcard\Domain\Exceptions\SessionFlashcardAlreadyRatedException;

test('construct session finished fail', function () {
    // GIVEN
    // THEN
    $this->expectException(SessionFinishedException::class);

    // WHEN
    $this->model = new RateableSessionFlashcards(
        new SessionId(1),
        UserId::fromString(Uuid::make()->getValue()),
        new FlashcardDeckId(1),
        SessionStatus::FINISHED,
        9,
        10,
        []
    );
});
test('rate should set rating for correct flashcard', function () {
    // GIVEN
    $expected_id = new SessionFlashcardId(5);

    $this->model = new RateableSessionFlashcards(
        new SessionId(1),
        UserId::fromString(Uuid::make()->getValue()),
        new FlashcardDeckId(1),
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
    expect($this->model->getRateableSessionFlashcards()[1]->getRating())->toBe(Rating::GOOD);
});
test('rate rate not existing id fail', function () {
    // GIVEN
    $expected_id = new SessionFlashcardId(5);

    $this->model = new RateableSessionFlashcards(
        new SessionId(1),
        UserId::fromString(Uuid::make()->getValue()),
        new FlashcardDeckId(1),
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
});
test('rate when flashcard already rated fail', function () {
    // GIVEN
    $expected_id = new SessionFlashcardId(5);

    $this->model = new RateableSessionFlashcards(
        new SessionId(1),
        UserId::fromString(Uuid::make()->getValue()),
        new FlashcardDeckId(1),
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
});
