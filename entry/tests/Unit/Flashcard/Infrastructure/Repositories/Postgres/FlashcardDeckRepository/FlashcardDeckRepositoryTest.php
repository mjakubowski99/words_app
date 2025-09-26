<?php

declare(strict_types=1);
use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Story;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use App\Models\StoryFlashcard;
use Shared\Enum\Language;
use Shared\Enum\LanguageLevel;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Deck;
use Shared\Enum\FlashcardOwnerType;
use App\Models\FlashcardDeckActivity;
use Shared\Utils\ValueObjects\UserId;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardDeckRepository::class);
});
test('find by id when normal deck success', function () {
    // GIVEN
    $other_deck = FlashcardDeck::factory()->create();
    $deck = FlashcardDeck::factory()->create();

    // WHEN
    $result = $this->repository->findById($deck->getId());

    // THEN
    expect($result)->toBeInstanceOf(Deck::class)
        ->and($result->getId()->getValue())->toBe($deck->getId()->getValue())
        ->and($result->getName())->toBe($deck->name)
        ->and($result->getOwner()->getId()->getValue())->toBe($deck->user->getId()->getValue())
        ->and($result->getOwner()->getOwnerType())->toBe(FlashcardOwnerType::USER);
});
test('find by id when admin is owner success', function () {
    // GIVEN
    $admin = Admin::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => null,
        'admin_id' => $admin->id,
    ]);

    // WHEN
    $result = $this->repository->findById($deck->getId());

    // THEN
    expect($result)->toBeInstanceOf(Deck::class)
        ->and($result->getOwner()->equals($admin->toOwner()))->toBeTrue();
});
test('create should create deck', function () {
    // GIVEN
    $deck = Mockery::mock(Deck::class);
    $user = User::factory()->create();
    $deck->allows([
        'getName' => 'Cat name',
        'hasOwner' => true,
        'getOwner' => $user->toOwner(),
        'getDefaultLanguageLevel' => LanguageLevel::A1,
    ]);

    // WHEN
    $this->repository->create($deck);

    // THEN
    $this->assertDatabaseHas('flashcard_decks', [
        'name' => 'Cat name',
        'user_id' => $user->id,
    ]);
});
test('create when admin is owner should create deck', function () {
    // GIVEN
    $deck = Mockery::mock(Deck::class);
    $admin = Admin::factory()->create();
    $deck->allows([
        'getName' => 'Cat name',
        'hasOwner' => true,
        'getOwner' => $admin->toOwner(),
        'getDefaultLanguageLevel' => LanguageLevel::A1,
    ]);

    // WHEN
    $this->repository->create($deck);

    // THEN
    $this->assertDatabaseHas('flashcard_decks', [
        'name' => 'Cat name',
        'admin_id' => $admin->id,
    ]);
});
test('update when user is owner should update deck', function () {
    // GIVEN
    $deck_model = FlashcardDeck::factory()->create();

    $deck = Mockery::mock(Deck::class);
    $user = User::factory()->create();
    $deck->allows([
        'getId' => $deck_model->getId(),
        'getName' => 'Cat',
        'hasOwner' => true,
        'getOwner' => $user->toOwner(),
        'getDefaultLanguageLevel' => LanguageLevel::A1,
    ]);

    // WHEN
    $this->repository->update($deck);

    // THEN
    $this->assertDatabaseHas('flashcard_decks', [
        'id' => $deck_model->id,
        'name' => 'Cat',
        'admin_id' => null,
        'user_id' => $user->id,
    ]);
});
test('update when admin is owner should update deck', function () {
    // GIVEN
    $deck_model = FlashcardDeck::factory()->create();

    $deck = Mockery::mock(Deck::class);
    $admin = Admin::factory()->create();
    $deck->allows([
        'getId' => $deck_model->getId(),
        'getName' => 'Cat name',
        'hasOwner' => true,
        'getOwner' => $admin->toOwner(),
        'getDefaultLanguageLevel' => LanguageLevel::A1,
    ]);

    // WHEN
    $this->repository->update($deck);

    // THEN
    $this->assertDatabaseHas('flashcard_decks', [
        'id' => $deck_model->id,
        'admin_id' => $admin->id,
        'user_id' => null,
    ]);
});
test('update last viewed at no entry in database', function () {
    // GIVEN
    Carbon::setTestNow('2023-10-12 12:00');
    $deck = FlashcardDeck::factory()->create();
    $user = User::factory()->create();

    // WHEN
    $this->repository->updateLastViewedAt($deck->getId(), $user->getId());

    // THEN
    $this->assertDatabaseHas(FlashcardDeckActivity::class, [
        'flashcard_deck_id' => $deck->getId(),
        'user_id' => $user->getId(),
        'last_viewed_at' => '2023-10-12 12:00',
    ]);
});
test('update last viewed at entry in database', function () {
    // GIVEN
    $activity = FlashcardDeckActivity::factory()->create();
    Carbon::setTestNow('2023-10-12 12:00');

    // WHEN
    $this->repository->updateLastViewedAt(new FlashcardDeckId($activity->flashcard_deck_id), new UserId($activity->user_id));

    // THEN
    $this->assertDatabaseHas(FlashcardDeckActivity::class, [
        'id' => $activity->id,
        'last_viewed_at' => '2023-10-12 12:00',
    ]);
});
test('get by user return only user decks', function () {
    // GIVEN
    $user = User::factory()->create();
    $admin = Admin::factory()->create();
    $other_deck = FlashcardDeck::factory()->create([
        'admin_id' => $admin->id,
        'user_id' => null,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $other_deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);
    $user_deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
        'admin_id' => null,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $user_deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), Language::PL, Language::EN, 1, 15);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(Deck::class)
        ->and($results[0]->getId()->getValue())->toBe($user_deck->id)
        ->and($results[0]->getName())->toBe($user_deck->name)
        ->and($results[0]->getOwner()->getId()->getValue())->toBe($user_deck->user_id)
        ->and($results[0]->getOwner()->getOwnerType())->toBe(FlashcardOwnerType::USER);
});
test('get by owner pagination works', function () {
    // GIVEN
    $user = User::factory()->create();
    $user_decks = FlashcardDeck::factory(2)->create([
        'user_id' => $user->id,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $user_decks[0]->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $user_decks[1]->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), Language::PL, Language::EN, 2, 1);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(Deck::class)
        ->and($results[0]->getId()->getValue())->toBe($user_decks[1]->id);
});
test('search by name should return user deck', function () {
    // GIVEN
    $user = User::factory()->create();
    $other_deck = FlashcardDeck::factory()->create(['name' => 'deck']);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $other_deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);
    $expected_deck = FlashcardDeck::factory()->create(['name' => 'deck', 'user_id' => $user->id]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $expected_deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

    // WHEN
    $deck = $this->repository->searchByName($user->getId(), 'deck', Language::PL, Language::EN);

    // THEN
    expect($deck->getId()->getValue())->toBe($expected_deck->id)
        ->and($deck->getName())->toBe($expected_deck->name)
        ->and($deck->getOwner()->getId()->getValue())->toBe($user->id);
});
test('search by name should correctly search by name', function () {
    // GIVEN
    $user = User::factory()->create();
    $other_deck = FlashcardDeck::factory()->create(['name' => 'deck 1', 'user_id' => $user->id]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $other_deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);
    $expected_deck = FlashcardDeck::factory()->create(['name' => 'deck', 'user_id' => $user->id]);
    Flashcard::factory()->create([
        'flashcard_deck_id' => $expected_deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::EN,
    ]);

    // WHEN
    $deck = $this->repository->searchByName($user->getId(), 'deck', Language::PL, Language::EN);

    // THEN
    expect($deck->getId()->getValue())->toBe($expected_deck->id)
        ->and($deck->getName())->toBe($expected_deck->name);
    });
test('bulk delete should delete only decks with given ids', function () {
    // GIVEN
    $user = User::factory()->create();
    $decks_to_delete = FlashcardDeck::factory(2)->byUser($user)->create();
    $decks_to_not_delete = FlashcardDeck::factory()->byUser($user)->create();
    $deck_ids = $decks_to_delete->map(fn (FlashcardDeck $deck) => $deck->getId())->toArray();

    // WHEN
    $this->repository->bulkDelete($user->getId(), $deck_ids);

    // THEN
    $this->assertDatabaseHas('flashcard_decks', [
        'id' => $decks_to_not_delete->id,
    ]);
    foreach ($decks_to_delete as $deck) {
        $this->assertDatabaseMissing('flashcard_decks', [
            'id' => $deck->id,
        ]);
    }
});
test('bulk delete should delete only user decks', function () {
    // GIVEN
    $user = User::factory()->create();
    $user_deck = FlashcardDeck::factory()->byUser($user)->create();
    $other_deck = FlashcardDeck::factory()->create();

    // WHEN
    $this->repository->bulkDelete($user->getId(), [$user_deck->getId(), $other_deck->getId()]);

    // THEN
    $this->assertDatabaseHas('flashcard_decks', [
        'id' => $other_deck->id,
    ]);
    $this->assertDatabaseMissing('flashcard_decks', [
        'id' => $user_deck->id,
    ]);
});
test('bulk delete should delete deck with all data', function () {
    // GIVEN
    $user = User::factory()->create();
    $user_deck = FlashcardDeck::factory()->byUser($user)->create();
    $flashcard = Flashcard::factory()->create(['flashcard_deck_id' => $user_deck->id]);
    $sm_two_flashcard = SmTwoFlashcard::factory()->create([
        'flashcard_id' => $flashcard->id,
    ]);
    $learning_session = LearningSession::factory()->create(['flashcard_deck_id' => $user_deck->id]);
    $learning_session_flashcard_from_session = LearningSessionFlashcard::factory()->create([
        'learning_session_id' => $learning_session->id,
    ]);
    $learning_session_flashcard = LearningSessionFlashcard::factory()->create([
        'flashcard_id' => $flashcard->id,
    ]);
    StoryFlashcard::factory()->create([
        'flashcard_id' => $flashcard->id,
        'story_id' => Story::factory()->create()->id,
    ]);

    // WHEN
    $this->repository->bulkDelete($user->getId(), [$user_deck->getId()]);

    // THEN
    $this->assertDatabaseMissing('flashcard_decks', [
        'id' => $user_deck->id,
    ]);
    $this->assertDatabaseMissing('sm_two_flashcards', [
        'flashcard_id' => $flashcard->id,
    ]);
    $this->assertDatabaseMissing('flashcards', [
        'id' => $flashcard->id,
    ]);
    $this->assertDatabaseMissing('learning_sessions', [
        'id' => $learning_session->id,
    ]);
    $this->assertDatabaseMissing('learning_session_flashcards', [
        'id' => $learning_session_flashcard->id,
    ]);
    $this->assertDatabaseMissing('learning_session_flashcards', [
        'id' => $learning_session_flashcard_from_session->id,
    ]);
});
