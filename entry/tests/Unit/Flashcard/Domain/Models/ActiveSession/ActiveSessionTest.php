<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\ActiveSession;

use Tests\TestCase;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\ActiveSession;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Exceptions\SessionFlashcardAlreadyRatedException;

class ActiveSessionTest extends TestCase
{
    use ActiveSessionTrait;

    private ActiveSession $session;

    public function test__rate_WhenFlashcardExists_ShouldUpdateRating(): void
    {
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
        $this->assertEquals(Rating::GOOD, $session->get($session_flashcard_id)->getRating());
    }

    /**
     * @dataProvider scoreDataProvider
     */
    public function test__rateByExerciseScore_WhenFlashcardExists_ShouldUpdateRating(float $score, Rating $expected_rating): void
    {
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
        $this->assertEquals($expected_rating, $session->get($session_flashcard_id)->getRating());
    }

    public function test__rate_WhenFlashcardAlreadyRated_ShouldThrowException(): void
    {
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
    }

    public function test__get_WhenFlashcardDoesNotExist_ShouldReturnNull(): void
    {
        // GIVEN
        $session_flashcard_id = new SessionFlashcardId(1);
        $session = $this->createActiveSession();

        // WHEN
        $result = $session->get($session_flashcard_id);

        // THEN
        $this->assertNull($result);
    }

    public function test__getRatedSessionFlashcardIds_WhenFlashcardsExist_ShouldReturnOnlyRated(): void
    {
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
        $this->assertCount(1, $flashcard_ids);
        $this->assertEquals($rated_flashcard->getSessionFlashcardId(), $flashcard_ids[0]);
    }
}
