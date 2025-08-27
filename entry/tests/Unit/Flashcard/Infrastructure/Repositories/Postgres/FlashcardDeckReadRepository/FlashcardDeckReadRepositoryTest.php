<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Admin;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Tests\Base\FlashcardTestCase;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardDeckReadRepository::class);
});

test('find details success', function () {
    // GIVEN
    $deck = $this->createFlashcardDeck();
    $flashcard = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
    ]);

    // WHEN
    $result = $this->repository->findDetails($deck->getUserId(), $deck->getId(), null, 1, 15);

    // THEN
    expect($result)->toBeInstanceOf(DeckDetailsRead::class)
        ->and($result->getId()->getValue())->toBe($deck->getId()->getValue())
        ->and($result->getName())->toBe($deck->name)
        ->and($result->getFlashcards())->toHaveCount(1)
        ->and($result->getOwnerType())->toBe(FlashcardOwnerType::USER)
        ->and($result->getLastLearntAt())->toBeNull()
        ->and($result->getRatingPercentage())->toBe(0.0)
        ->and($result->getLanguageLevel()->value)->toBe($deck->default_language_level)
        ->and($result->getFlashcards()[0])->toBeInstanceOf(FlashcardRead::class)
        ->and($result->getFlashcards()[0]->getId()->getValue())->toBe($flashcard->id)
        ->and($result->getFlashcards()[0]->getFrontWord())->toBe($flashcard->front_word)
        ->and($result->getFlashcards()[0]->getFrontLang()->getValue())->toBe($flashcard->front_lang)
        ->and($result->getFlashcards()[0]->getBackWord())->toBe($flashcard->back_word)
        ->and($result->getFlashcards()[0]->getBackLang()->getValue())->toBe($flashcard->back_lang)
        ->and($result->getFlashcards()[0]->getFrontContext())->toBe($flashcard->front_context)
        ->and($result->getFlashcards()[0]->getBackContext())->toBe($flashcard->back_context)
        ->and($result->getFlashcards()[0]->getOwnerType())->toBe(FlashcardOwnerType::USER)
        ->and($result->getFlashcards()[0]->getGeneralRating()->getValue())->toBe(GeneralRatingType::NEW)
        ->and($result->getPage())->toBe(1)
        ->and($result->getPerPage())->toBe(15)
        ->and($result->getFlashcardsCount())->toBe(1);
});
test('find details when admin is deck owner success', function () {
    // GIVEN
    $user = $this->createUser();
    $deck = $this->createFlashcardDeck([
        'user_id' => null,
        'admin_id' => Admin::factory()->create()->id,
    ]);
    $flashcard = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'user_id' => null,
        'admin_id' => Admin::factory()->create()->id,
    ]);

    // WHEN
    $result = $this->repository->findDetails($user->getId(), $deck->getId(), null, 1, 15);

    // THEN
    expect($result)->toBeInstanceOf(DeckDetailsRead::class)
        ->and($result->getOwnerType())->toBe(FlashcardOwnerType::ADMIN)
        ->and($result->getFlashcards()[0]->getId()->getValue())->toBe($flashcard->id)
        ->and($result->getFlashcards()[0]->getOwnerType())->toBe(FlashcardOwnerType::ADMIN);
});

test('find details general rating is last rating', function () {
    // GIVEN
    $now = now();
    $deck = $this->createFlashcardDeck();
    $flashcard = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
    ]);
    $learning_session = $this->createLearningSession([
        'user_id' => $deck->getUserId(),
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'flashcard_id' => $flashcard->id,
        'rating' => Rating::WEAK,
        'updated_at' => (clone $now)->subMinute(),
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'flashcard_id' => $flashcard->id,
        'rating' => Rating::GOOD,
        'updated_at' => $now,
    ]);

    // WHEN
    $result = $this->repository->findDetails($deck->getUserId(), $deck->getId(), null, 1, 15);

    // THEN
    expect($result)->toBeInstanceOf(DeckDetailsRead::class)
        ->and($result->getId()->getValue())->toBe($deck->getId()->getValue())
        ->and($result->getName())->toBe($deck->name)
        ->and($result->getFlashcards())->toHaveCount(1)
        ->and($result->getFlashcards()[0]->getGeneralRating()->getValue())->toBe(GeneralRatingType::GOOD);
});

test('find details search works', function () {
    // GIVEN
    $deck = $this->createFlashcardDeck();
    $other = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'front_word' => 'Pen',
    ]);
    $expected = $this->createFlashcard([
        'flashcard_deck_id' => $deck->id,
        'front_word' => 'Apple',
    ]);

    // WHEN
    $result = $this->repository->findDetails($deck->getUserId(), $deck->getId(), 'pple', 1, 15);

    // THEN
    expect($result)->toBeInstanceOf(DeckDetailsRead::class)
        ->and($result->getFlashcards())->toHaveCount(1)
        ->and($result->getFlashcards()[0])->toBeInstanceOf(FlashcardRead::class)
        ->and($result->getFlashcards()[0]->getId()->getValue())->toBe($expected->id);
});

test('get by user return only user categories', function () {
    // GIVEN
    $user = $this->createUser();
    $other_deck = $this->createFlashcardDeck();
    $user_deck = $this->createFlashcardDeck([
        'user_id' => $user->id,
        'default_language_level' => LanguageLevel::B1,
    ]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), null, 1, 15);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(OwnerCategoryRead::class)
        ->and($results[0]->getId()->getValue())->toBe($user_deck->id)
        ->and($results[0]->getName())->toBe($user_deck->name)
        ->and($results[0]->getLanguageLevel()->value)->toBe($user_deck->default_language_level->value)
        ->and($results[0]->getOwnerType())->toBe(FlashcardOwnerType::USER);
});

test('get by user pagination works', function () {
    // GIVEN
    $user = $this->createUser();
    $this->createFlashcardDeck([
        'user_id' => $user->id,
    ]);
    $user_deck = $this->createFlashcardDeck([
        'user_id' => $user->id,
    ]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), null, 2, 1);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(OwnerCategoryRead::class)
        ->and($results[0]->getId()->getValue())->toBe($user_deck->id);
});

test('get by user searching works', function () {
    // GIVEN
    $user = $this->createUser();
    $other = $this->createFlashcardDeck([
        'user_id' => $user->id,
        'name' => 'Nal',
    ]);
    $expected = $this->createFlashcardDeck([
        'user_id' => $user->id,
        'name' => 'Alan',
    ]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(OwnerCategoryRead::class)
        ->and($results[0]->getId()->getValue())->toBe($expected->id);
});

test('get admin decks searching works', function () {
    // GIVEN
    $user = $this->createUser();
    $other = $this->createFlashcardDeck([
        'user_id' => null,
        'admin_id' => Admin::factory()->create(),
        'name' => 'Nal',
    ]);
    $expected = $this->createFlashcardDeck([
        'user_id' => null,
        'admin_id' => Admin::factory()->create(),
        'name' => 'Alan',
    ]);

    // WHEN
    $results = $this->repository->getAdminDecks($user->getId(), null, 'LAn', 1, 15);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(OwnerCategoryRead::class)
        ->and($results[0]->getId()->getValue())->toBe($expected->id)
        ->and($results[0]->getOwnerType())->toBe(FlashcardOwnerType::ADMIN);
});

test('get admin decks language level filter works', function () {
    // GIVEN
    $user = $this->createUser();
    $other = $this->createFlashcardDeck([
        'user_id' => null,
        'admin_id' => Admin::factory()->create(),
        'name' => 'Nal',
        'default_language_level' => LanguageLevel::A2,
    ]);
    $expected = $this->createFlashcardDeck([
        'user_id' => null,
        'admin_id' => Admin::factory()->create(),
        'name' => 'Alan',
        'default_language_level' => LanguageLevel::A1,
    ]);

    // WHEN
    $results = $this->repository->getAdminDecks($user->getId(), LanguageLevel::A1, 'LAn', 1, 15);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(OwnerCategoryRead::class)
        ->and($results[0]->getId()->getValue())->toBe($expected->id);
});

test('get admin decks last learnt at is correct', function () {
    // GIVEN
    $now = now();
    $user = $this->createUser();
    $expected = $this->createFlashcardDeck([
        'user_id' => null,
        'admin_id' => Admin::factory()->create()->id,
        'name' => 'Alan',
    ]);
    $flashcards = [
        $this->createFlashcard(['flashcard_deck_id' => $expected->id]),
        $this->createFlashcard(['flashcard_deck_id' => $expected->id]),
    ];
    $learning_session = $this->createLearningSession([
        'user_id' => $user->id,
    ]);
    $this->createLearningSessionFlashcard([
        'flashcard_id' => $flashcards[0]->id,
        'updated_at' => (clone $now)->subMinute(),
        'learning_session_id' => $learning_session->id,
    ]);
    $this->createLearningSessionFlashcard([
        'flashcard_id' => $flashcards[0]->id,
        'updated_at' => $now,
        'learning_session_id' => $learning_session->id,
    ]);

    // WHEN
    $results = $this->repository->getAdminDecks($user->getId(), null, 'LAn', 1, 15);

    // THEN
    expect($results[0]->getLastLearntAt()->toDateTimeString())->toBe($now->toDateTimeString());
});

test('get by user level is most frequent flashcard language level', function () {
    // GIVEN
    $user = $this->createUser();
    $expected = $this->createFlashcardDeck([
        'user_id' => $user->id,
        'name' => 'Alan',
    ]);
    $this->createFlashcard(['flashcard_deck_id' => $expected->id, 'language_level' => LanguageLevel::A1]);
    $this->createFlashcard(['flashcard_deck_id' => $expected->id, 'language_level' => LanguageLevel::A2]);
    $this->createFlashcard(['flashcard_deck_id' => $expected->id, 'language_level' => LanguageLevel::A2]);
    $this->createFlashcard(['flashcard_deck_id' => $expected->id, 'language_level' => LanguageLevel::A2]);
    $this->createFlashcard(['flashcard_deck_id' => $expected->id, 'language_level' => LanguageLevel::B1]);
    $this->createFlashcard(['flashcard_deck_id' => $expected->id, 'language_level' => LanguageLevel::B1]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

    // THEN
    expect($results)->toHaveCount(1)
        ->and($results[0])->toBeInstanceOf(OwnerCategoryRead::class)
        ->and($results[0]->getLanguageLevel())->toBe(LanguageLevel::A2)
        ->and($results[0]->getFlashcardsCount())->toBe(6);
});

test('get by user rating percentage is correct', function () {
    // GIVEN
    $user = $this->createUser();
    $expected = $this->createFlashcardDeck([
        'user_id' => $user->id,
        'name' => 'Alan',
    ]);
    $flashcards = [
        $this->createFlashcard(['flashcard_deck_id' => $expected->id]),
        $this->createFlashcard(['flashcard_deck_id' => $expected->id]),
    ];
    $session = $this->createLearningSession(['user_id' => $user->id]);
    $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'flashcard_id' => $flashcards[0]->id, 'rating' => Rating::GOOD]);
    $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'flashcard_id' => $flashcards[0]->id, 'rating' => Rating::UNKNOWN]);
    $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'flashcard_id' => $flashcards[0]->id, 'rating' => Rating::VERY_GOOD]);

    $expected_ratio = (
        (Rating::VERY_GOOD->value + Rating::UNKNOWN->value) / 2
    ) / (2 * Rating::VERY_GOOD->value) * 100.0;

    // WHEN
    $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

    // THEN
    expect(round($results[0]->getRatingPercentage(), 2))->toBe(round($expected_ratio, 2));
});

test('get by user last learnt at is correct', function () {
    // GIVEN
    $now = now();
    $user = $this->createUser();
    $expected = $this->createFlashcardDeck([
        'user_id' => $user->id,
        'name' => 'Alan',
    ]);
    $flashcards = [
        $this->createFlashcard(['flashcard_deck_id' => $expected->id]),
        $this->createFlashcard(['flashcard_deck_id' => $expected->id]),
    ];
    $session = $this->createLearningSession(['user_id' => $user->id]);
    $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'flashcard_id' => $flashcards[0]->id, 'updated_at' => (clone $now)->subMinute()]);
    $this->createLearningSessionFlashcard(['learning_session_id' => $session->id, 'flashcard_id' => $flashcards[0]->id, 'updated_at' => $now]);

    // WHEN
    $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

    // THEN
    expect($results[0]->getLastLearntAt()->toDateTimeString())->toBe($now->toDateTimeString());
});

test('find rating stats return correct values', function (array $flashcards, array $expecteds) {
    // GIVEN
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create([
        'user_id' => $user->id,
    ]);
    $session = $this->createLearningSession([
        'flashcard_deck_id' => $deck->id,
    ]);
    foreach ($flashcards as $flashcard) {
        if ($flashcard['rating'] === null) {
            $this->createFlashcard(['flashcard_deck_id' => $deck->id]);
        } else {
            $this->createLearningSessionFlashcard([
                'learning_session_id' => $session->id,
                'rating' => $flashcard['rating'],
                'flashcard_id' => $this->createFlashcard(['flashcard_deck_id' => $deck->id])->id,
            ]);
        }
    }

    // WHEN
    $results = $this->repository->findRatingStats($deck->getId());

    // THEN
    $i = 0;
    foreach ($results->getRatingStats() as $result) {
        expect($result->getRating()->getValue()->value)
            ->toBe($expecteds[$i]['rating'])
            ->and(round($result->getRatingPercentage(), 2))
            ->toBe($expecteds[$i]['rating_percentage']);
        ++$i;
    }
})->with('ratedFlashcardsRatingProvider');

dataset('ratedFlashcardsRatingProvider', function () {
    return [
        'case 1' => [
            'flashcards' => [
                ['rating' => Rating::GOOD],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::VERY_GOOD],
            ],
            'expecteds' => [
                ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
                ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 0.0],
                ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 66.67],
                ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 33.33],
            ],
        ],
        'case 2' => [
            'flashcards' => [
                ['rating' => Rating::UNKNOWN],
                ['rating' => Rating::WEAK],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::VERY_GOOD],
            ],
            'expecteds' => [
                ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 25.0],
                ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 25.0],
                ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 25.0],
                ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 25.0],
            ],
        ],
        'case 3' => [
            'flashcards' => [
                ['rating' => Rating::UNKNOWN],
                ['rating' => Rating::WEAK],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::VERY_GOOD],
            ],
            'expecteds' => [
                ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 20.0],
                ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 20.0],
                ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 40.0],
                ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 20.0],
            ],
        ],
        'case 4' => [
            'flashcards' => [
                ['rating' => Rating::UNKNOWN],
                ['rating' => Rating::WEAK],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::VERY_GOOD],
                ['rating' => Rating::VERY_GOOD],
            ],
            'expecteds' => [
                ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 16.67],
                ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 16.67],
                ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 33.33],
                ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 33.33],
            ],
        ],
        'case 5' => [
            'flashcards' => [
                ['rating' => Rating::UNKNOWN],
                ['rating' => Rating::WEAK],
                ['rating' => Rating::GOOD],
                ['rating' => Rating::GOOD],
                ['rating' => null],
            ],
            'expecteds' => [
                ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 25.0],
                ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 25.0],
                ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 50.0],
                ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 0.0],
            ],
        ],
    ];
});
