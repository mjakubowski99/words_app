<?php

declare(strict_types=1);

use App\Models\FlashcardPollItem;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\LeitnerLevelUpdate;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardPollRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardPollRepository::class);
});

test('find by user when nothing for user return fresh object', function () {
    // GIVEN
    $user_id = new UserId(Uuid::make()->getValue());

    // WHEN
    $result = $this->repository->findByUser($user_id, 1);

    // THEN
    expect($result)->toBeInstanceOf(FlashcardPoll::class);
});

test('find by user when user has flashcard in poll find object', function () {
    // GIVEN
    $user = $this->createUser();
    $poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
    ]);

    // WHEN
    $result = $this->repository->findByUser($user->getId(), 1);

    // THEN
    expect($result->getUserId()->getValue())->toBe($poll_item->user_id);
    expect($result)->toBeInstanceOf(FlashcardPoll::class);
});
test('find by user when flashcard poll has flashcards to purge return correct flashcards to purge', function () {
    // GIVEN
    $user = $this->createUser();
    $poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
        'easy_ratings_count' => 3,
        'easy_ratings_count_to_purge' => 4,
    ]);
    $poll_item_to_reject = $this->createFlashcardPollItem([
        'user_id' => $user->id,
        'easy_ratings_count' => 4,
        'easy_ratings_count_to_purge' => 4,
    ]);

    // WHEN
    $result = $this->repository->findByUser($user->getId(), 5);

    // THEN
    expect($result->getUserId()->getValue())->toBe($poll_item->user_id);
    expect($result->getPurgeCandidates())->toHaveCount(1);
    expect($result->getPurgeCandidates()[0]->getValue())->toBe($poll_item_to_reject->flashcard_id);
});

test('save are flashcards to replace purge flashcards to purge and add new', function () {
    // GIVEN
    $user = $this->createUser();
    $flashcard = $this->createFlashcard();
    $poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
    ]);
    $poll = new FlashcardPoll(
        $user->getId(),
        1,
        FlashcardIdCollection::fromArray([new FlashcardId($poll_item->flashcard_id)])
    );
    $poll->replaceWithNew(FlashcardIdCollection::fromArray([$flashcard->getId()]));

    // WHEN
    $this->repository->save($poll);

    // THEN
    $this->assertDatabaseMissing(FlashcardPollItem::class, [
        'user_id' => $poll_item->user_id,
        'flashcard_id' => $poll_item->flashcard_id,
    ]);
    $this->assertDatabaseHas(FlashcardPollItem::class, [
        'user_id' => $poll_item->user_id,
        'flashcard_id' => $flashcard->id,
    ]);
});
test('save add flashcards to add add flashcards to add', function () {
    // GIVEN
    $user = $this->createUser();
    $flashcard = $this->createFlashcard();
    $poll = new FlashcardPoll(
        $user->getId(),
        1,
        new FlashcardIdCollection([]),
        new FlashcardIdCollection([new FlashcardId($flashcard->id)]),
    );

    // WHEN
    $this->repository->save($poll);

    // THEN
    $this->assertDatabaseHas(FlashcardPollItem::class, [
        'user_id' => $user->id,
        'flashcard_id' => $flashcard->id,
    ]);
});

test('select next leitner flashcards return correct flashcard', function (array $flashcards, string $expected) {
    // GIVEN
    $user = $this->createUser();

    foreach ($flashcards as $flashcard) {
        $this->createFlashcardPollItem([
            'user_id' => $user->id,
            'flashcard_id' => $this->createFlashcard(['front_word' => $flashcard['name']]),
            'leitner_level' => $flashcard['leitner_level'],
            'updated_at' => $flashcard['updated_at'],
        ]);
    }

    // WHEN
    $flashcards = $this->repository->selectNextLeitnerFlashcard($user->getId(), [], 1);

    // THEN
    expect($flashcards)->toHaveCount(1);
    $this->assertDatabaseHas('flashcards', [
        'id' => $flashcards[0]->getValue(),
        'front_word' => $expected,
    ]);
})->with('dataProvider');

dataset('dataProvider', function () {
    yield 'Many flashcards with lowest level' => [
        'flashcards' => [
            ['name' => 'A', 'leitner_level' => 0, 'updated_at' => now()],
            ['name' => 'C', 'leitner_level' => 0, 'updated_at' => now()->subMinute()],
            ['name' => 'B', 'leitner_level' => 1, 'updated_at' => now()],
        ],
        'expected' => 'C',
    ];

    yield 'Flashcards from different leitner levels' => [
        'flashcards' => [
            ['name' => 'G', 'leitner_level' => 1, 'updated_at' => now()],
            ['name' => 'B', 'leitner_level' => 2, 'updated_at' => now()->subMinute()],
        ],
        'expected' => 'G',
    ];
});

test('reset leitner level if max level exceeded when reset needed reset', function () {
    // GIVEN
    $user = $this->createUser();

    $poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
        'leitner_level' => 30000,
    ]);
    $other_item = $this->createFlashcardPollItem([
        'leitner_level' => 30000,
    ]);

    // WHEN
    $this->repository->resetLeitnerLevelIfMaxLevelExceeded($user->getId(), Rating::maxLeitnerLevel());

    // THEN
    $this->assertDatabaseHas('flashcard_poll_items', [
        'id' => $poll_item->id,
        'leitner_level' => 0,
    ]);
    $this->assertDatabaseHas('flashcard_poll_items', [
        'id' => $other_item->id,
        'leitner_level' => 30000,
    ]);
});

test('reset leitner level if max level exceeded when reset not needed do not reset levels', function () {
    // GIVEN
    $user = $this->createUser();

    $poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
        'leitner_level' => 2,
    ]);

    // WHEN
    $this->repository->resetLeitnerLevelIfMaxLevelExceeded($user->getId(), Rating::maxLeitnerLevel());

    // THEN
    $this->assertDatabaseHas('flashcard_poll_items', [
        'id' => $poll_item->id,
        'leitner_level' => 2,
    ]);
});

test('save leitner level update increment correctly', function () {
    // GIVEN
    $user = $this->createUser();
    $step = Rating::maxRating();

    $other_poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
    ]);

    $poll_item = $this->createFlashcardPollItem([
        'user_id' => $user->id,
        'leitner_level' => 2,
        'easy_ratings_count' => 3,
    ]);

    $update = new LeitnerLevelUpdate(
        $user->getId(),
        FlashcardIdCollection::fromArray([new FlashcardId($poll_item->flashcard_id)]),
        $step
    );

    // WHEN
    $this->repository->saveLeitnerLevelUpdate($update);

    // THEN
    $this->assertDatabaseHas('flashcard_poll_items', [
        'id' => $poll_item->id,
        'leitner_level' => 2 + $step + 1,
        'easy_ratings_count' => 4,
    ]);
    $this->assertDatabaseHas('flashcard_poll_items', [
        'id' => $other_poll_item->id,
        'leitner_level' => $other_poll_item->leitner_level,
        'easy_ratings_count' => $other_poll_item->easy_ratings_count,
    ]);
});

test('purge latest flashcards remove only latest cards', function () {
    // GIVEN
    $user = $this->createUser();

    $poll_item_to_keep = $this->createFlashcardPollItem([
        'user_id' => $user->id,
        'created_at' => '2024-01-12 13:00',
    ]);
    $poll_items_to_purge = [
        $this->createFlashcardPollItem([
            'user_id' => $user->id,
            'created_at' => '2024-01-12 14:00',
        ]),
        $this->createFlashcardPollItem([
            'user_id' => $user->id,
            'created_at' => '2024-01-12 15:00',
        ]),
    ];

    // WHEN
    $this->repository->purgeLatestFlashcards($user->getId(), count($poll_items_to_purge));

    // THEN
    $this->assertDatabaseHas('flashcard_poll_items', [
        'id' => $poll_item_to_keep->id,
    ]);
    foreach ($poll_items_to_purge as $item) {
        $this->assertDatabaseMissing('flashcard_poll_items', [
            'id' => $item->id,
        ]);
    }
});
