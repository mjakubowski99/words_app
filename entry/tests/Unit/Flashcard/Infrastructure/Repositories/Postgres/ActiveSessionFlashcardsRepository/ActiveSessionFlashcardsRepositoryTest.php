<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionFlashcardsRepository;

use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Domain\Models\ActiveSessionFlashcards;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionFlashcardsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Tests\TestCase;

class ActiveSessionFlashcardsRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use ActiveSessionFlashcardsRepositoryTrait;

    private ActiveSessionFlashcardsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ActiveSessionFlashcardsRepository::class);
    }

    public function test__findBySessionFlashcardIds_ShouldFindCorrectFlashcards(): void
    {
        // GIVEN
        $session_flashcard_not_to_find = $this->createSessionFlashcard(['rating' => Rating::UNKNOWN]);
        $session_flashcards_to_find = [
            $this->createSessionFlashcard(['rating' => null]),
            $this->createSessionFlashcard(['rating' => Rating::GOOD]),
        ];
        $ids = $this->pluckLearningSessionFlashcardId($session_flashcards_to_find);

        // WHEN
        $result = $this->repository->findBySessionFlashcardIds($ids);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(2, $flashcards);

        foreach ($flashcards as $flashcard) {
            $expected_flashcard = $this->findBySessionFlashcardId($flashcard->getSessionFlashcardId(), $session_flashcards_to_find);

            $this->assertNotNull($expected_flashcard);
            $this->assertSame($expected_flashcard->flashcard_id, $flashcard->getFlashcardId()->getValue());
        }
    }

    public function test__findBySessionFlashcardIds_returnsCorrectRatings(): void
    {
        // GIVEN
        $session_flashcards = [
            $this->createSessionFlashcard(['rating' => Rating::UNKNOWN]),
            $this->createSessionFlashcard(['rating' => Rating::GOOD]),
        ];
        $ids = $this->pluckLearningSessionFlashcardId($session_flashcards);

        // WHEN
        $result = $this->repository->findBySessionFlashcardIds($ids);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(2, $flashcards);

        foreach ($flashcards as $flashcard) {
            $expected_flashcard = $this->findBySessionFlashcardId($flashcard->getSessionFlashcardId(), $session_flashcards);

            $this->assertNotNull($expected_flashcard);
            $this->assertSame($expected_flashcard->rating->value, $flashcard->getRating()->value);
        }
    }

    public function test__findBySessionFlashcardIds_returnsCorrectSessionData(): void
    {
        // GIVEN
        $session_flashcards = [
            $this->createSessionFlashcard([
                'learning_session_id' => $this->createLearningSession([
                    'status' => SessionStatus::STARTED,
                    'cards_per_session' => 12,
                ]),
            ]),
            $this->createSessionFlashcard([
                'learning_session_id' => $this->createLearningSession([
                    'status' => SessionStatus::IN_PROGRESS,
                    'cards_per_session' => 14,
                ]),
            ]),
        ];
        $ids = $this->pluckLearningSessionFlashcardId($session_flashcards);

        // WHEN
        $result = $this->repository->findBySessionFlashcardIds($ids);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(2, $flashcards);

        foreach ($flashcards as $flashcard) {
            $expected_flashcard = $this->findBySessionFlashcardId($flashcard->getSessionFlashcardId(), $session_flashcards);

            $this->assertNotNull($expected_flashcard);
            $this->assertSame($expected_flashcard->learning_session_id, $flashcard->getSessionId()->getValue());
            $this->assertSame($expected_flashcard->session->status, $flashcard->getSessionStatus()->value);
            $this->assertSame($expected_flashcard->session->cards_per_session, $flashcard->getMaxCount());
        }
    }

    public function test__findBySessionFlashcardIds_filtersOutFinishedSessions(): void
    {
        // GIVEN
        $session_flashcard = $this->createSessionFlashcard([
            'learning_session_id' => $this->createLearningSession([
                'status' => SessionStatus::FINISHED,
                'cards_per_session' => 12,
            ]),
        ]);

        // WHEN
        $result = $this->repository->findBySessionFlashcardIds([$session_flashcard->getId()]);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(0, $flashcards);
    }

    public function test__findBySessionFlashcardIds_sessionNotFinished_findSession(): void
    {
        // GIVEN
        $session_flashcard = $this->createSessionFlashcard([
            'learning_session_id' => $this->createLearningSession([
                'status' => SessionStatus::IN_PROGRESS,
                'cards_per_session' => 12,
            ]),
        ]);

        // WHEN
        $result = $this->repository->findBySessionFlashcardIds([$session_flashcard->getId()]);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(1, $flashcards);
    }

    public function test__findBySessionFlashcardIds_ratedCountIsCorrect(): void
    {
        // GIVEN
        $session = $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS->value]);

        $this->createSessionFlashcard([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);
        $this->createSessionFlashcard([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD,
        ]);
        $session_flashcard = $this->createSessionFlashcard([
            'learning_session_id' => $session->id,
            'rating' => Rating::UNKNOWN,
        ]);


        // WHEN
        $result = $this->repository->findBySessionFlashcardIds([$session_flashcard->getId()]);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(1, $flashcards);
        $this->assertSame($session_flashcard->id, $flashcards[$session_flashcard->id]->getSessionFlashcardId()->getValue());
        $this->assertSame(2, $flashcards[$session_flashcard->id]->getRatedCount());
    }

    public function test__findBySessionFlashcardIds_ratedCountDoNotCountsAdditionalFlashcards(): void
    {
        // GIVEN
        $session = $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS->value]);

        $this->createSessionFlashcard([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD,
            'is_additional' => true,
        ]);
        $session_flashcard = $this->createSessionFlashcard([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD,
            'is_additional' => false,
        ]);

        // WHEN
        $result = $this->repository->findBySessionFlashcardIds([$session_flashcard->getId()]);

        // THEN
        $flashcards = $result->all();
        $this->assertCount(1, $flashcards);
        $this->assertSame($session_flashcard->id, $flashcards[$session_flashcard->id]->getSessionFlashcardId()->getValue());
        $this->assertSame(1, $flashcards[$session_flashcard->id]->getRatedCount());
    }

    public function test__findLatestRatings_WhenFlashcardHasManyPreviousRatings_findLatest(): void
    {
        // GIVEN
        $flashcard = $this->createFlashcard();
        $expected_rating = Rating::UNKNOWN;
        $this->createSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => $expected_rating,
            'updated_at' => now()->subMinutes(5),
        ]);
        $this->createSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::GOOD,
            'updated_at' => now()->subHours(10),
        ]);
        $session_flashcards = [
            $this->createSessionFlashcard([
                'flashcard_id' => $flashcard->id,
                'rating' => null,
            ]),
            $this->createSessionFlashcard([
                'rating' => null,
            ])
        ];
        $ids = $this->pluckLearningSessionFlashcardId($session_flashcards);

        // WHEN
        $result = $this->repository->findLatestRatings($ids);

        // THEN
        $index = $session_flashcards[0]->flashcard_id;
        $this->assertSame($expected_rating, $result[$index]);
    }

    public function test__findLatestRatings_WhenFlashcardHasNoRatings_returnEmptyArray(): void
    {
        // GIVEN
        $flashcard = $this->createFlashcard();
        $session_flashcards = [
            $this->createSessionFlashcard([
                'flashcard_id' => $flashcard->id,
                'rating' => null,
            ]),
        ];
        $ids = $this->pluckLearningSessionFlashcardId($session_flashcards);

        // WHEN
        $result = $this->repository->findLatestRatings($ids);

        // THEN
        $this->assertCount(0, $result);
    }

    public function test__save_ShouldSaveFlashcardRatings(): void
    {
        // GIVEN
        $session_flashcards = [
            $this->createSessionFlashcard([
                'rating' => null,
            ]),
            $this->createSessionFlashcard([
                'rating' => null,
            ]),
        ];
        $domain_flashcards = [
            new ActiveSessionFlashcard(
                new SessionId(1),
                SessionStatus::IN_PROGRESS,
                UserId::new(),
                1,
                $session_flashcards[0]->getId(),
                new FlashcardId(1),
                0,
                Rating::GOOD,
                false,
            ),
            new ActiveSessionFlashcard(
                new SessionId(1),
                SessionStatus::IN_PROGRESS,
                UserId::new(),
                1,
                $session_flashcards[1]->getId(),
                new FlashcardId(1),
                0,
                Rating::UNKNOWN,
                false,
            )
        ];
        $domain_collection = new ActiveSessionFlashcards($domain_flashcards);


        // WHEN
        $this->repository->save($domain_collection);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcards[0]->getId(),
            'rating' => Rating::GOOD->value,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcards[1]->getId(),
            'rating' => Rating::UNKNOWN->value,
        ]);
    }
}
