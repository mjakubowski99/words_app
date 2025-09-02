<?php

declare(strict_types=1);
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\ActiveSession;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionRepository;
use Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionFlashcardsRepository\ActiveSessionRepositoryTrait;

uses(DatabaseTransactions::class);

uses(ActiveSessionRepositoryTrait::class);

beforeEach(function () {
    $this->repository = $this->app->make(ActiveSessionRepository::class);
});

test('find by exercise entry ids should find correct flashcards', function () {
    // GIVEN
    $exercise_entry_ids = [1, 2];
    $session_flashcard_not_to_find = $this->createSessionFlashcard(['rating' => Rating::UNKNOWN]);
    $session_flashcards_to_find = [
        $this->createSessionFlashcard([
            'rating' => null,
            'exercise_entry_id' => $exercise_entry_ids[0],
            'learning_session_id' => $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS]),
        ]),
        $this->createSessionFlashcard([
            'rating' => Rating::GOOD,
            'exercise_entry_id' => $exercise_entry_ids[1],
            'learning_session_id' => $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS]),
        ]),
    ];

    // WHEN
    /** @var ActiveSession[] $sessions */
    $sessions = $this->repository->findByExerciseEntryIds($exercise_entry_ids);

    // THEN
    expect($sessions)->toHaveCount(2)
        ->and($sessions[0]->getSessionFlashcards())->toHaveCount(1)
        ->and($sessions[1]->getSessionFlashcards())->toHaveCount(1)
        ->and($sessions[0])->toBeInstanceOf(ActiveSession::class)
        ->and($sessions[0])->toBeInstanceOf(ActiveSession::class)
        ->and($sessions[0]->getSessionFlashcards()[0]->getSessionFlashcardId()->getValue())->toBe($session_flashcards_to_find[0]->id)
        ->and($sessions[1]->getSessionFlashcards()[0]->getSessionFlashcardId()->getValue())->toBe($session_flashcards_to_find[1]->id);
});

test('correct update poll status', function () {
    // GIVEN
    $exercise_entry_ids = [1];
    $learning_session = $this->createLearningSession([
        'status' => SessionStatus::IN_PROGRESS,
        'flashcard_deck_id' => null,
    ]);
    $flashcard = $this->createSessionFlashcard([
        'rating' => null,
        'exercise_entry_id' => $exercise_entry_ids[0],
        'learning_session_id' => $learning_session->id,
    ]);

    // WHEN
    /** @var ActiveSession[] $sessions */
    $sessions = $this->repository->findByExerciseEntryIds($exercise_entry_ids);

    // THEN
    expect($sessions)->toHaveCount(1)
        ->and($sessions[0]->updatePoll(new SessionFlashcardId($flashcard->id)))
        ->toBeTrue();
});

test('find by exercise entry ids returns correct ratings', function () {
    // GIVEN
    $exercise_entry_ids = [1, 2];
    $session = $this->createLearningSession(['status' => SessionStatus::IN_PROGRESS]);
    $session_flashcards = [
        $this->createSessionFlashcard(['rating' => Rating::UNKNOWN, 'learning_session_id' => $session->id, 'exercise_entry_id' => $exercise_entry_ids[0]]),
        $this->createSessionFlashcard(['rating' => Rating::GOOD, 'learning_session_id' => $session->id, 'exercise_entry_id' => $exercise_entry_ids[0]]),
    ];

    // WHEN
    $sessions = $this->repository->findByExerciseEntryIds($exercise_entry_ids);

    // THEN
    expect($sessions)->toHaveCount(1)
        ->and($sessions[0]->getSessionFlashcards())->toHaveCount(2);

    foreach ($sessions[0]->getSessionFlashcards() as $flashcard) {
        $expected_flashcard = $this->findBySessionFlashcardId($flashcard->getSessionFlashcardId(), $session_flashcards);

        expect($expected_flashcard)
            ->not->toBeNull()
            ->and($flashcard->getRating()->value)
            ->toBe($expected_flashcard->rating->value);
    }
});

test('find by exercise entry ids returns correct session data', function () {
    // GIVEN
    $exercise_entry_ids = [1, 2];
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
    expect($sessions)->toHaveCount(2)
        ->and($sessions[0]->getSessionFlashcards())->toHaveCount(1)
        ->and($sessions[1]->getSessionFlashcards())->toHaveCount(1)
        ->and($sessions[0]->getSessionId()->getValue())->toBe($session_flashcards[0]->learning_session_id)
        ->and($sessions[0]->getMaxCount())->toBe($session_flashcards[0]->session->cards_per_session)
        ->and($sessions[1]->getSessionId()->getValue())->toBe($session_flashcards[1]->learning_session_id)
        ->and($sessions[1]->getMaxCount())->toBe($session_flashcards[1]->session->cards_per_session);
});
test('find by exercise entry ids filters out finished sessions', function () {
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
    expect($sessions)->toHaveCount(0);
});
test('find by exercise entry ids session not finished do not filter out', function () {
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
    expect($sessions)->toHaveCount(1);
});
test('find by exercise entry ids rated count is correct', function () {
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
        'exercise_entry_id' => 5,
    ]);

    // WHEN
    $sessions = $this->repository->findByExerciseEntryIds([$exercise_entry_id]);

    // THEN
    expect($sessions)->toHaveCount(1);
    expect($sessions[0]->getRatedCount())->toBe(2);
});
test('find by exercise entry ids rated count do not counts additional flashcards', function () {
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
    expect($sessions)->toHaveCount(1);
    expect($sessions[0]->getRatedCount())->toBe(1);
});
test('find latest ratings when flashcard has many previous ratings find latest', function () {
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
        ]),
    ];
    $ids = $this->pluckLearningSessionFlashcardId($session_flashcards);

    // WHEN
    $result = $this->repository->findLatestRatings($ids);

    // THEN
    $index = $session_flashcards[0]->flashcard_id;
    expect($result[$index])->toBe($expected_rating);
});
test('find latest ratings when flashcard has no ratings return empty array', function () {
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
    expect($result)->toHaveCount(0);
});
test('save should save flashcard ratings', function () {
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
        ),
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
});
test('save when session finished should finish session', function () {
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
            ),
        ]
    );

    // WHEN
    $this->repository->save($session);

    // THEN
    $this->assertDatabaseHas('learning_sessions', [
        'id' => $session->getSessionId()->getValue(),
        'status' => SessionStatus::FINISHED->value,
    ]);
});
