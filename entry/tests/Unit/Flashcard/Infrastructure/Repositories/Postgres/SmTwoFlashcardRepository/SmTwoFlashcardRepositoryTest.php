<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Admin;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use App\Models\FlashcardPollItem;
use Shared\Enum\Language;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Repository\FlashcardSortCriteria;
use Flashcard\Infrastructure\Repositories\Postgres\SmTwoFlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(SmTwoFlashcardRepository::class);
});
test('find many should return correct sm two flashcards', function () {
    // GIVEN
    $user = User::factory()->create();
    $expected_flashcards = [
        SmTwoFlashcard::factory()->create(['user_id' => $user->id, 'min_rating' => 3]),
        SmTwoFlashcard::factory()->create(['user_id' => $user->id]),
    ];
    $other_flashcards = SmTwoFlashcard::factory()->create(['user_id' => $user->id]);
    $flashcard_ids = array_map(fn (SmTwoFlashcard $flashcard) => $flashcard->getFlashcardId(), $expected_flashcards);

    // WHEN
    $results = $this->repository->findMany($user->getId(), $flashcard_ids);

    // THEN
    expect(count($results))->toBe(2)
        ->and($results->all()[0]->getMinRating())->toBe(3);
});
test('reset repetitions in session success', function () {
    // GIVEN
    $user = User::factory()->create();
    $other_flashcard = SmTwoFlashcard::factory()->create([
        'repetitions_in_session' => 4,
    ]);
    $flashcard = SmTwoFlashcard::factory()->create([
        'user_id' => $user->getId()->getValue(),
        'repetitions_in_session' => 3,
    ]);

    // WHEN
    $this->repository->resetRepetitionsInSession($user->getId());

    // THEN
    $this->assertDatabaseHas('sm_two_flashcards', [
        'flashcard_id' => $flashcard->flashcard_id,
        'repetitions_in_session' => 0,
    ]);
    $this->assertDatabaseHas('sm_two_flashcards', [
        'flashcard_id' => $other_flashcard->flashcard_id,
        'repetitions_in_session' => 4,
    ]);
});
test('save many should save', function () {
    // GIVEN
    $user = User::factory()->create();
    $flashcard = SmTwoFlashcard::factory()->create([
        'user_id' => $user->getId()->getValue(),
        'repetition_interval' => 1,
        'repetition_count' => 2,
        'repetition_ratio' => 3,
        'min_rating' => 2,
        'repetitions_in_session' => 3,
    ]);
    $domain_model = new SmTwoFlashcards([
        $flashcard->toDomainModel(),
    ]);

    // WHEN
    $this->repository->saveMany($domain_model);

    // THEN
    $this->assertDatabaseHas('sm_two_flashcards', [
        'flashcard_id' => $flashcard->flashcard_id,
        'user_id' => $flashcard->user_id,
        'repetition_ratio' => $flashcard->repetition_ratio,
        'repetition_interval' => $flashcard->repetition_interval,
        'repetition_count' => $flashcard->repetition_count,
        'min_rating' => $flashcard->min_rating,
        'repetitions_in_session' => $flashcard->repetitions_in_session,
    ]);
});
test('get next flashcards by deck should return flashcards', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();

    $flashcard = Flashcard::factory()->create(['flashcard_deck_id' => $deck->id]);
    $sm_two_flashcards = [
        SmTwoFlashcard::factory()->create([
            'flashcard_id' => Flashcard::factory()->create([
                'flashcard_deck_id' => $deck->id,
                'user_id' => null,
                'admin_id' => Admin::factory()->create()->id,
            ]),
            'user_id' => $user->id,
            'repetition_interval' => 2,
            'updated_at' => now()->subDays(3),
        ]),
        SmTwoFlashcard::factory()->create([
            'flashcard_id' => Flashcard::factory()->create([
                'flashcard_deck_id' => $deck->id,
                'user_id' => $user->id,
                'admin_id' => null,
            ]),
            'user_id' => $user->id,
            'repetition_interval' => 3,
            'updated_at' => now()->subDays(3),
        ]),
    ];

    $flashcard_poll = FlashcardPollItem::factory()->create([
        'user_id' => $user->id,
        'flashcard_id' => $sm_two_flashcards[1]->flashcard_id,
    ]);

    // WHEN
    $results = $this->repository->getNextFlashcardsByDeck($user->getId(), $deck->getId(), 5, [], [
        FlashcardSortCriteria::HARD_FLASHCARDS_FIRST,
        FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
        FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
        FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER,
        FlashcardSortCriteria::NOT_RATED_FLASHCARDS_FIRST,
        FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER,
        FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST,
        FlashcardSortCriteria::LOWEST_REPETITION_INTERVAL_FIRST,
    ], 2, true, Language::PL, Language::EN);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0]->getId()->getValue())->toBe($flashcard_poll->flashcard_id);
});

test('get next flashcards by deck return flashcards in correct language', function () {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();

    $flashcard = Flashcard::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::PL,
        'back_lang' => Language::ZH,
    ]);
    $expected_flashcard = Flashcard::factory()->create([
        'user_id' => $user->id,
        'flashcard_deck_id' => $deck->id,
        'front_lang' => Language::DE,
        'back_lang' => Language::ES,
    ]);

    // WHEN
    $results = $this->repository->getNextFlashcardsByDeck($user->getId(), $deck->getId(), 5, [], [FlashcardSortCriteria::HARD_FLASHCARDS_FIRST], 2, false, Language::DE, Language::ES);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0]->getId()->getValue())
        ->toBe($expected_flashcard->id)
        ->and($results[0]->getFrontLang()->getValue())
        ->toBe(Language::DE->value)
        ->and($results[0]->getBackLang()->getValue())
        ->toBe(Language::ES->value);
});
