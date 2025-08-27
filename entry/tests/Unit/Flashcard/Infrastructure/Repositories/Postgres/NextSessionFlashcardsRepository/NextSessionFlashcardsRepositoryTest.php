<?php

declare(strict_types=1);
use App\Models\Admin;
use App\Models\Flashcard;
use Shared\Enum\SessionType;
use App\Models\FlashcardDeck;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\NextSessionFlashcardsRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(NextSessionFlashcardsRepository::class);
});
test('find no flashcards should find correct object', function () {
    // GIVEN
    $user = $this->createUser();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getUnratedCount())->toBe(0);
    expect($result->getCurrentSessionFlashcardsCount())->toBe(0);
});
test('find has flashcards should find correct object', function () {
    // GIVEN
    $session = LearningSession::factory()->create([
        'flashcard_deck_id' => null,
    ]);
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => Rating::GOOD,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getUnratedCount())->toBe(1);
    expect($result->getCurrentSessionFlashcardsCount())->toBe(2);
});
test('find when no deck should find correct object', function () {
    // GIVEN
    $session = LearningSession::factory()->create([
        'flashcard_deck_id' => null,
    ]);
    LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $session->id,
        'rating' => null,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getUserId()->getValue())->toBe($session->user_id);
    expect($result->hasDeck())->toBeFalse();
});
test('find multiple decks should find correct object', function () {
    // GIVEN
    $user = $this->createUser();
    $decks = FlashcardDeck::factory(2)->create([
        'user_id' => $user->id,
    ]);
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $decks[1]->id,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getSessionId()->getValue())->toBe($session->id);
    expect($result->getDeck()->getId()->getValue())->toBe($decks[1]->id);
    expect($result->getDeck()->getName())->toBe($decks[1]->name);
    expect($result->getDeck()->getTag())->toBe($decks[1]->tag);
});
test('find admin flashcard should find correct object', function () {
    // GIVEN
    $user = $this->createUser();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => null,
        'admin_id' => Admin::factory()->create()->id,
    ]);
    $session = LearningSession::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $result = $this->repository->find($session->getId());

    // THEN
    expect($result->getDeck()->getOwner()->isAdmin())->toBeTrue();
    expect($result->getDeck()->getOwner()->getId()->getValue())->toBe($deck->admin_id);
});
test('save should save object', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    $flashcard = Flashcard::factory()->create();
    $object = new NextSessionFlashcards(
        $session->getId(),
        SessionType::FLASHCARD,
        $session->user->getId(),
        $session->deck->toDomainModel(),
        8,
        2,
        10
    );
    $object->addNext($flashcard->toDomainModel());

    // WHEN
    $this->repository->save($object);

    // THEN
    $this->assertDatabaseHas('learning_session_flashcards', [
        'learning_session_id' => $session->id,
        'flashcard_id' => $flashcard->id,
        'rating' => null,
        'is_additional' => false,
    ]);
});
test('save when additional flashcards should save object', function () {
    // GIVEN
    $session = LearningSession::factory()->create();
    $flashcard = Flashcard::factory()->create();
    $object = new NextSessionFlashcards(
        $session->getId(),
        SessionType::FLASHCARD,
        $session->user->getId(),
        $session->deck->toDomainModel(),
        8,
        2,
        10
    );
    $object->addNextAdditional($flashcard->toDomainModel());

    // WHEN
    $this->repository->save($object);

    // THEN
    $this->assertDatabaseHas('learning_session_flashcards', [
        'learning_session_id' => $session->id,
        'flashcard_id' => $flashcard->id,
        'rating' => null,
        'is_additional' => true,
    ]);
});
