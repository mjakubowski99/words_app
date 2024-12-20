<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

use App\Models\User;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Tests\Base\FlashcardTestCase;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

class FlashcardDeckReadRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;
    private FlashcardDeckReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardDeckReadRepository::class);
    }

    public function test__findDetails_success(): void
    {
        // GIVEN
        $deck = $this->createFlashcardDeck();
        $flashcard = $this->createFlashcard([
            'flashcard_deck_id' => $deck->id,
        ]);

        // WHEN
        $result = $this->repository->findDetails($deck->getId(), null, 1, 15);

        // THEN
        $this->assertInstanceOf(DeckDetailsRead::class, $result);
        $this->assertSame($deck->getId()->getValue(), $result->getId()->getValue());
        $this->assertSame($deck->name, $result->getName());
        $this->assertCount(1, $result->getFlashcards());
        $this->assertInstanceOf(FlashcardRead::class, $result->getFlashcards()[0]);
        $this->assertSame($flashcard->id, $result->getFlashcards()[0]->getId()->getValue());
        $this->assertSame($flashcard->front_word, $result->getFlashcards()[0]->getFrontWord());
        $this->assertSame($flashcard->front_lang, $result->getFlashcards()[0]->getFrontLang()->getValue());
        $this->assertSame($flashcard->back_word, $result->getFlashcards()[0]->getBackWord());
        $this->assertSame($flashcard->back_lang, $result->getFlashcards()[0]->getBackLang()->getValue());
        $this->assertSame($flashcard->front_context, $result->getFlashcards()[0]->getFrontContext());
        $this->assertSame($flashcard->back_context, $result->getFlashcards()[0]->getBackContext());
        $this->assertSame(GeneralRatingType::NEW, $result->getFlashcards()[0]->getGeneralRating()->getValue());
        $this->assertSame(1, $result->getPage());
        $this->assertSame(15, $result->getPerPage());
        $this->assertSame(1, $result->getFlashcardsCount());
    }

    public function test__findDetails_generalRatingIsLastRating(): void
    {
        // GIVEN
        $now = now();
        $deck = $this->createFlashcardDeck();
        $flashcard = $this->createFlashcard([
            'flashcard_deck_id' => $deck->id,
        ]);
        $this->createLearningSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::WEAK,
            'updated_at' => (clone $now)->subMinute(),
        ]);
        $this->createLearningSessionFlashcard([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::GOOD,
            'updated_at' => $now,
        ]);

        // WHEN
        $result = $this->repository->findDetails($deck->getId(), null, 1, 15);

        // THEN
        $this->assertInstanceOf(DeckDetailsRead::class, $result);
        $this->assertSame($deck->getId()->getValue(), $result->getId()->getValue());
        $this->assertSame($deck->name, $result->getName());
        $this->assertCount(1, $result->getFlashcards());
        $this->assertSame(GeneralRatingType::GOOD, $result->getFlashcards()[0]->getGeneralRating()->getValue());
    }

    public function test__findDetails_searchWorks(): void
    {
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
        $result = $this->repository->findDetails($deck->getId(), 'pple', 1, 15);

        // THEN
        $this->assertInstanceOf(DeckDetailsRead::class, $result);
        $this->assertCount(1, $result->getFlashcards());
        $this->assertInstanceOf(FlashcardRead::class, $result->getFlashcards()[0]);
        $this->assertSame($expected->id, $result->getFlashcards()[0]->getId()->getValue());
    }

    public function test__getByOwner_ReturnOnlyUserCategories(): void
    {
        // GIVEN
        $user = $this->createUser();
        $other_deck = $this->createFlashcardDeck();
        $user_deck = $this->createFlashcardDeck([
            'user_id' => $user->id,
            'default_language_level' => LanguageLevel::B1,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), null, 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_deck->id, $results[0]->getId()->getValue());
        $this->assertSame($user_deck->name, $results[0]->getName());
        $this->assertSame($user_deck->default_language_level->value, $results[0]->getLanguageLevel()->value);
    }

    public function test__getByOwner_paginationWorks(): void
    {
        // GIVEN
        $user = $this->createUser();
        $this->createFlashcardDeck([
            'user_id' => $user->id,
        ]);
        $user_deck = $this->createFlashcardDeck([
            'user_id' => $user->id,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), null, 2, 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_deck->id, $results[0]->getId()->getValue());
    }

    public function test__getByOwner_searchingWorks(): void
    {
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
        $results = $this->repository->getByOwner($user->toOwner(), 'LAn', 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($expected->id, $results[0]->getId()->getValue());
    }

    public function test__getByOwner_levelIsMostFrequentFlashcardLanguageLevel(): void
    {
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
        $results = $this->repository->getByOwner($user->toOwner(), 'LAn', 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame(LanguageLevel::A2, $results[0]->getLanguageLevel());
        $this->assertSame(6, $results[0]->getFlashcardsCount());
    }

    public function test__getByOwner_ratingRatioIsCorrect(): void
    {
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
        $this->createLearningSessionFlashcard(['flashcard_id' => $flashcards[0]->id, 'rating' => Rating::GOOD]);
        $this->createLearningSessionFlashcard(['flashcard_id' => $flashcards[0]->id, 'rating' => Rating::UNKNOWN]);
        $this->createLearningSessionFlashcard(['flashcard_id' => $flashcards[0]->id, 'rating' => Rating::VERY_GOOD]);

        $expected_ratio = (
            (Rating::UNKNOWN->value / Rating::VERY_GOOD->value)
            + (Rating::VERY_GOOD->value / Rating::VERY_GOOD->value)
            + (Rating::GOOD->value / Rating::VERY_GOOD->value)
        ) / 3;

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), 'LAn', 1, 15);

        // THEN
        $this->assertSame(round($expected_ratio, 2), round($results[0]->getRatingRatio(), 2));
    }

    public function test__getByOwner_lastLearntAtIsCorrect(): void
    {
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
        $this->createLearningSessionFlashcard(['flashcard_id' => $flashcards[0]->id, 'updated_at' => (clone $now)->subMinute()]);
        $this->createLearningSessionFlashcard(['flashcard_id' => $flashcards[0]->id, 'updated_at' => $now]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), 'LAn', 1, 15);

        // THEN
        $this->assertSame($now->toDateTimeString(), $results[0]->getLastLearntAt()->toDateTimeString());
    }

    /**
     * @dataProvider ratedFlashcardsRatingProvider
     */
    public function test__findRatingStats_returnCorrectValues(
        array $flashcards,
        array $expecteds
    ): void {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
        ]);
        $session = $this->createLearningSession([
            'flashcard_deck_id' => $deck->id,
        ]);
        foreach ($flashcards as $flashcard) {
            $this->createLearningSessionFlashcard([
                'learning_session_id' => $session->id,
                'rating' => $flashcard['rating'],
                'flashcard_id' => $this->createFlashcard(['flashcard_deck_id' => $deck->id])->id,
            ]);
        }

        // WHEN
        $results = $this->repository->findRatingStats($deck->getId());

        // THEN
        $i = 0;
        foreach ($results->getRatingStats() as $result) {
            $this->assertSame($expecteds[$i]['rating'], $result->getRating()->getValue()->value);
            $this->assertSame($expecteds[$i]['rating_percentage'], round($result->getRatingPercentage(), 2));
            ++$i;
        }
    }

    public static function ratedFlashcardsRatingProvider(): array
    {
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
    }
}
