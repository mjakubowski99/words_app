<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionFlashcardsRepository;

use Flashcard\Domain\Models\ActiveSession;
use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\UserId;
use Tests\TestCase;

class ActiveSessionRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use ActiveSessionRepositoryTrait;

    private ActiveSessionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ActiveSessionRepository::class);
    }

    public function test__findByExerciseEntryIds_ShouldFindCorrectFlashcards(): void
    {
        // GIVEN
        $exercise_entry_ids = [1,2];
        $session_flashcard_not_to_find = $this->createSessionFlashcard(['rating' => Rating::UNKNOWN]);
        $session_flashcards_to_find = [
            $this->createSessionFlashcard([
                'rating' => null,
                'exercise_entry_id' => $exercise_entry_ids[0],
                'learning_session_id' => $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS])
            ]),
            $this->createSessionFlashcard([
                'rating' => Rating::GOOD,
                'exercise_entry_id' => $exercise_entry_ids[1],
                'learning_session_id' => $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS]),
            ]),
        ];

        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds($exercise_entry_ids);

        // THEN
        $this->assertCount(2, $sessions);
        $this->assertCount(1, $sessions[0]->getSessionFlashcards());
        $this->assertCount(1, $sessions[1]->getSessionFlashcards());

        $this->assertSame($session_flashcards_to_find[0]->id, $sessions[0]->getSessionFlashcards()[0]->getSessionFlashcardId()->getValue());
        $this->assertSame($session_flashcards_to_find[1]->id, $sessions[1]->getSessionFlashcards()[0]->getSessionFlashcardId()->getValue());
    }

    public function test__findByExerciseEntryIds_returnsCorrectRatings(): void
    {
        // GIVEN
        $exercise_entry_ids = [1,2];
        $session = $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS]);
        $session_flashcards = [
            $this->createSessionFlashcard(['rating' => Rating::UNKNOWN, 'learning_session_id' => $session->id, 'exercise_entry_id' => $exercise_entry_ids[0]]),
            $this->createSessionFlashcard(['rating' => Rating::GOOD, 'learning_session_id' => $session->id, 'exercise_entry_id' => $exercise_entry_ids[0]]),
        ];

        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds($exercise_entry_ids);

        // THEN
        $this->assertCount(1, $sessions);
        $this->assertCount(2, $sessions[0]->getSessionFlashcards());

        foreach ($sessions[0]->getSessionFlashcards() as $flashcard) {
            $expected_flashcard = $this->findBySessionFlashcardId($flashcard->getSessionFlashcardId(), $session_flashcards);

            $this->assertNotNull($expected_flashcard);
            $this->assertSame($expected_flashcard->rating->value, $flashcard->getRating()->value);
        }
    }

    public function test__findByExerciseEntryIds_returnsCorrectSessionData(): void
    {
        // GIVEN
        $exercise_entry_ids = [1,2];
        $session_flashcards = [
            $this->createSessionFlashcard([
                'learning_session_id' => $this->createLearningSession([
                    'status' => SessionStatus::STARTED,
                    'cards_per_session' => 12,
                ]),
                'exercise_entry_id' => $exercise_entry_ids[0],
            ]),
            $this->createSessionFlashcard([
                'learning_session_id' => $this->createLearningSession([
                    'status' => SessionStatus::IN_PROGRESS,
                    'cards_per_session' => 14,
                ]),
                'exercise_entry_id' => $exercise_entry_ids[1],
            ]),
        ];
        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds($exercise_entry_ids);

        // THEN
        $this->assertCount(2, $sessions);
        $this->assertCount(1, $sessions[0]->getSessionFlashcards());
        $this->assertCount(1, $sessions[1]->getSessionFlashcards());

        $this->assertSame($session_flashcards[0]->learning_session_id, $sessions[0]->getSessionId()->getValue());
        $this->assertSame($session_flashcards[0]->session->cards_per_session, $sessions[0]->getMaxCount());

        $this->assertSame($session_flashcards[1]->learning_session_id, $sessions[1]->getSessionId()->getValue());
        $this->assertSame($session_flashcards[1]->session->cards_per_session, $sessions[1]->getMaxCount());
    }

    public function test__findByExerciseEntryIds_filtersOutFinishedSessions(): void
    {
        // GIVEN
        $exercise_entry_id = 4;
        $this->createSessionFlashcard([
            'learning_session_id' => $this->createLearningSession([
                'status' => SessionStatus::FINISHED,
                'cards_per_session' => 12,
            ]),
            'exercise_entry_id' => $exercise_entry_id,
        ]);

        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds([$exercise_entry_id]);

        // THEN
        $this->assertCount(0, $sessions);
    }

    public function test__findByExerciseEntryIds_SessionNotFinished_doNotFilterOut(): void
    {
        // GIVEN
        $exercise_entry_id = 4;
        $this->createSessionFlashcard([
            'learning_session_id' => $this->createLearningSession([
                'status' => SessionStatus::IN_PROGRESS,
                'cards_per_session' => 12,
            ]),
            'exercise_entry_id' => $exercise_entry_id,
        ]);

        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds([$exercise_entry_id]);

        // THEN
        $this->assertCount(1, $sessions);
    }

    public function test__findByExerciseEntryIds_ratedCountIsCorrect(): void
    {
        // GIVEN
        $exercise_entry_id = 5;
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
            'exercise_entry_id' =>5,
        ]);

        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds([$exercise_entry_id]);

        // THEN
        $this->assertCount(1, $sessions);
        $this->assertSame(2, $sessions[0]->getRatedCount());
    }

    public function test__findByExerciseEntryIds_ratedCountDoNotCountsAdditionalFlashcards(): void
    {
        // GIVEN
        $exercise_entry_id = 9;
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
            'exercise_entry_id' => $exercise_entry_id,
        ]);

        // WHEN
        $sessions = $this->repository->findByExerciseEntryIds([$exercise_entry_id]);

        // THEN
        $this->assertCount(1, $sessions);
        $this->assertSame(1, $sessions[0]->getRatedCount());
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
        $flashcards = [
            new ActiveSessionFlashcard(
                $session_flashcards[0]->getId(),
                new FlashcardId(1),
                Rating::GOOD,
                12,
                false,
            ),
            new ActiveSessionFlashcard(
                $session_flashcards[1]->getId(),
                new FlashcardId(1),
                Rating::UNKNOWN,
                null,
                false,
            )
        ];
        $session = new ActiveSession(
            new SessionId($session_flashcards[0]->learning_session_id),
            UserId::new(),
            12,
            2,
            false,
            $flashcards
        );

        // WHEN
        $this->repository->save($session);

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

    public function test__save_WhenSessionFinished_ShouldFinishSession(): void
    {
        // GIVEN
        $session = $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS]);
        $session = new ActiveSession(
            $session->getId(),
            UserId::new(),
            2,
            2,
            false,
            [
                new ActiveSessionFlashcard(
                    new SessionFlashcardId(1),
                    new FlashcardId(1),
                    Rating::UNKNOWN,
                    null,
                    false,
                )
            ]
        );

        // WHEN
        $this->repository->save($session);

        // THEN
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $session->getSessionId()->getValue(),
            'status' => SessionStatus::FINISHED->value,
        ]);
    }
}
