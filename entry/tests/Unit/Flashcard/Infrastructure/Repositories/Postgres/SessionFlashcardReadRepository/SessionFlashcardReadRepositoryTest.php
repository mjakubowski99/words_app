<?php

declare(strict_types=1);
use App\Models\Admin;
use App\Models\Flashcard;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\SessionFlashcardReadRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(SessionFlashcardReadRepository::class);
});
test('find unrated by id should return only unrated flashcards', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD->value,
    ]);
    $expected = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $result = $this->repository->findUnratedById($session->getId(), 5);
    $session_flashcards = $result->getSessionFlashcards();

    // THEN
    expect($result->getSessionFlashcards())->toHaveCount(1);
    expect($session_flashcards[0]->getId()->getValue())->toBe($expected->id);
    expect($session_flashcards[0]->getFrontWord())->toBe($expected->flashcard->front_word);
    expect($session_flashcards[0]->getFrontLang()->getValue())->toBe($expected->flashcard->front_lang);
    expect($session_flashcards[0]->getBackLang()->getValue())->toBe($expected->flashcard->back_lang);
    expect($session_flashcards[0]->getBackWord())->toBe($expected->flashcard->back_word);
    expect($session_flashcards[0]->getFrontContext())->toBe($expected->flashcard->front_context);
    expect($session_flashcards[0]->getBackContext())->toBe($expected->flashcard->back_context);
    expect($session_flashcards[0]->getLanguageLevel()->value)->toBe($expected->flashcard->language_level);
    expect($session_flashcards[0]->getEmoji()->toUnicode())->toBe($expected->flashcard->emoji);
    expect($session_flashcards[0]->getOwnerType())->toBe(FlashcardOwnerType::USER);
});
test('find unrated by id when no unrated flashcard correct exercise mode', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD->value,
        'exercise_entry_id' => 1,
    ]);

    // WHEN
    $result = $this->repository->findUnratedById($session->getId(), 5);

    // THEN
    expect($result->getSessionFlashcards())->toHaveCount(0);
    expect($result->getExerciseSummaries())->toHaveCount(0);
    expect($result->isExerciseMode())->toBeTrue();
});
test('find unrated by id admin is owner', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    $flashcard = Flashcard::factory()->byAdmin(Admin::factory()->create())->create();
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'flashcard_id' => $flashcard->id,
        'rating' => null,
    ]);

    // WHEN
    $result = $this->repository->findUnratedById($session->getId(), 5);
    $session_flashcards = $result->getSessionFlashcards();

    // THEN
    expect($result->getSessionFlashcards())->toHaveCount(1);
    expect($session_flashcards[0]->getOwnerType())->toBe(FlashcardOwnerType::ADMIN);
});
