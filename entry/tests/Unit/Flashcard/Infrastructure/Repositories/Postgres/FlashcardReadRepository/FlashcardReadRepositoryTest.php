<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Flashcard;
use Shared\Enum\Language;
use Tests\Base\FlashcardTestCase;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardReadRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardReadRepository::class);
});

test('find rating stats return only user ratings', function () {
    // GIVEN
    $user = $this->createUser();
    $learning_session = $this->createLearningSession([
        'user_id' => $user->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::GOOD,
        'flashcard_id' => $this->createFlashcard(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $this->createLearningSessionFlashcard([
        'rating' => Rating::WEAK,
        'flashcard_id' => $this->createFlashcard(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $expecteds = [
        ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 100.0],
        ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 0.0],
    ];

    // WHEN
    $results = $this->repository->findStatsByUser($user->getId(), Language::EN, Language::PL, null);

    // THEN
    $i = 0;
    foreach ($results->getRatingStats() as $result) {
        expect($result->getRating()->getValue()->value)->toBe($expecteds[$i]['rating'])
            ->and(round($result->getRatingPercentage(), 2))->toBe($expecteds[$i]['rating_percentage']);
        ++$i;
    }
});

test('find rating stats return only ratings in language', function () {
    // GIVEN
    $user = $this->createUser();
    $learning_session = $this->createLearningSession([
        'user_id' => $user->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::GOOD,
        'flashcard_id' => $this->createFlashcard(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::WEAK,
        'flashcard_id' => $this->createFlashcard(['front_lang' => Language::DE, 'back_lang' => Language::FR])->id,
    ]);
    $expecteds = [
        ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 100.0],
        ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 0.0],
    ];

    // WHEN
    $results = $this->repository->findStatsByUser($user->getId(), Language::EN, Language::PL, null);

    // THEN
    $i = 0;
    foreach ($results->getRatingStats() as $result) {
        expect($result->getRating()->getValue()->value)->toBe($expecteds[$i]['rating'])
            ->and(round($result->getRatingPercentage(), 2))->toBe($expecteds[$i]['rating_percentage']);
        ++$i;
    }
});

test('find rating stats when owner type admin return ratings only for admin flashcards', function () {
    // GIVEN
    $user = $this->createUser();
    $learning_session = $this->createLearningSession([
        'user_id' => $user->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::GOOD,
        'flashcard_id' => Flashcard::factory()->byAdmin(Admin::factory()->create())->create(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::WEAK,
        'flashcard_id' => Flashcard::factory()->byUser($user)->create(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $expecteds = [
        ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 100.0],
        ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 0.0],
    ];

    // WHEN
    $results = $this->repository->findStatsByUser($user->getId(), Language::EN, Language::PL, FlashcardOwnerType::ADMIN);

    // THEN
    $i = 0;
    foreach ($results->getRatingStats() as $result) {
        expect($result->getRating()->getValue()->value)->toBe($expecteds[$i]['rating'])
            ->and(round($result->getRatingPercentage(), 2))->toBe($expecteds[$i]['rating_percentage']);
        ++$i;
    }
});
test('find rating stats when owner type user return ratings only for user flashcards', function () {
    // GIVEN
    $user = $this->createUser();
    $learning_session = $this->createLearningSession([
        'user_id' => $user->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::GOOD,
        'flashcard_id' => Flashcard::factory()->byAdmin(Admin::factory()->create())->create(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $this->createLearningSessionFlashcard([
        'learning_session_id' => $learning_session->id,
        'rating' => Rating::WEAK,
        'flashcard_id' => Flashcard::factory()->byUser($user)->create(['front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
    ]);
    $expecteds = [
        ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 100.0],
        ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 0.0],
        ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 0.0],
    ];

    // WHEN
    $results = $this->repository->findStatsByUser($user->getId(), Language::EN, Language::PL, FlashcardOwnerType::USER);

    // THEN
    $i = 0;
    foreach ($results->getRatingStats() as $result) {
        expect($result->getRating()->getValue()->value)->toBe($expecteds[$i]['rating'])
            ->and(round($result->getRatingPercentage(), 2))->toBe($expecteds[$i]['rating_percentage']);
        ++$i;
    }
});

test('find rating stats return correct values', function (array $flashcards, array $expecteds) {
    // GIVEN
    $user = $this->createUser();
    $learning_session = $this->createLearningSession([
        'user_id' => $user->id,
    ]);
    foreach ($flashcards as $flashcard) {
        $this->createLearningSessionFlashcard([
            'learning_session_id' => $learning_session->id,
            'rating' => $flashcard['rating'],
            'flashcard_id' => $this->createFlashcard(['user_id' => $user->id, 'front_lang' => Language::EN, 'back_lang' => Language::PL])->id,
        ]);
    }

    // WHEN
    $results = $this->repository->findStatsByUser($user->getId(), Language::EN, Language::PL, null);

    // THEN
    $i = 0;
    foreach ($results->getRatingStats() as $result) {
        expect($result->getRating()->getValue()->value)->toBe($expecteds[$i]['rating'])
            ->and(round($result->getRatingPercentage(), 2))->toBe($expecteds[$i]['rating_percentage']);
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
    ];
});
