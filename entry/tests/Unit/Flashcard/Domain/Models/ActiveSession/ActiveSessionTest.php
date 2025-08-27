<?php

declare(strict_types=1);
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Exceptions\SessionFlashcardAlreadyRatedException;
use Tests\Unit\Flashcard\Domain\Models\ActiveSession\ActiveSessionTrait;

uses(ActiveSessionTrait::class);

test('rate when flashcard exists should update rating', function () {
    // GIVEN
    $session_flashcard = $this->createActiveSessionFlashcard([
        'rating' => null,
    ]);
    $session = $this->createActiveSession();
    $session->addSessionFlashcard($session_flashcard);
    $session_flashcard_id = $session_flashcard->getSessionFlashcardId();

    // WHEN
    $session->rate($session_flashcard_id, Rating::GOOD);

    // THEN
    expect($session->get($session_flashcard_id)->getRating())->toEqual(Rating::GOOD);
});
test('rate by exercise score when flashcard exists should update rating', function (float $score, Rating $expected_rating) {
    // GIVEN
    $session_flashcard = $this->createActiveSessionFlashcard([
        'rating' => null,
        'exercise_entry_id' => 10,
    ]);
    $session = $this->createActiveSession();
    $session->addSessionFlashcard($session_flashcard);
    $session_flashcard_id = $session_flashcard->getSessionFlashcardId();

    // WHEN
    $session->rateFlashcardsByExerciseScore($session_flashcard_id, $score);

    // THEN
    expect($session->get($session_flashcard_id)->getRating())->toEqual($expected_rating);
})->with([
    'good' => [0.8, Rating::GOOD],
    'very_good' => [0.9, Rating::VERY_GOOD],
    'unknown' => [0.2, Rating::UNKNOWN],
    'weak' => [0.4, Rating::WEAK],
]);

test('rate when flashcard already rated should throw exception', function () {
    // GIVEN
    $session_flashcard = $this->createActiveSessionFlashcard([
        'rating' => Rating::GOOD,
    ]);
    $session = $this->createActiveSession();
    $session->addSessionFlashcard($session_flashcard);
    $session_flashcard_id = $session_flashcard->getSessionFlashcardId();

    // THEN
    $this->expectException(SessionFlashcardAlreadyRatedException::class);

    // WHEN
    $session->rate($session_flashcard_id, Rating::GOOD);
});

test('get when flashcard does not exist should return null', function () {
    // GIVEN
    $session_flashcard_id = new SessionFlashcardId(1);
    $session = $this->createActiveSession();

    // WHEN
    $result = $session->get($session_flashcard_id);

    // THEN
    expect($result)->toBeNull();
});

test('get rated session flashcard ids when flashcards exist should return only rated', function () {
    // GIVEN
    $rated_flashcard = $this->createActiveSessionFlashcard([
        'session_flashcard_id' => new SessionFlashcardId(43),
        'rating' => Rating::GOOD,
    ]);
    $unrated_flashcard = $this->createActiveSessionFlashcard([
        'session_flashcard_id' => new SessionFlashcardId(44),
        'rating' => null,
    ]);
    $session = $this->createActiveSession();
    $session->addSessionFlashcard($rated_flashcard);
    $session->addSessionFlashcard($unrated_flashcard);

    // WHEN
    $flashcard_ids = $session->getRatedSessionFlashcardIds();

    // THEN
    expect($flashcard_ids)->toHaveCount(1)
        ->and($flashcard_ids[0])->toEqual($rated_flashcard->getSessionFlashcardId());
});
