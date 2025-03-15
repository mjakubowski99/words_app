<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

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
        $result = $this->repository->findDetails($deck->getUserId(), $deck->getId(), null, 1, 15);

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
        $this->assertSame(FlashcardOwnerType::USER, $result->getFlashcards()[0]->getOwnerType());
        $this->assertSame(GeneralRatingType::NEW, $result->getFlashcards()[0]->getGeneralRating()->getValue());
        $this->assertSame(1, $result->getPage());
        $this->assertSame(15, $result->getPerPage());
        $this->assertSame(1, $result->getFlashcardsCount());
    }

    public function test__findDetails_WhenAdminIsDeckOwner_success(): void
    {
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
        $this->assertInstanceOf(DeckDetailsRead::class, $result);
        $this->assertSame(FlashcardOwnerType::ADMIN, $result->getOwnerType());
        $this->assertSame($flashcard->id, $result->getFlashcards()[0]->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::ADMIN, $result->getFlashcards()[0]->getOwnerType());
    }

    public function test__findDetails_generalRatingIsLastRating(): void
    {
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
        $result = $this->repository->findDetails($deck->getUserId(), $deck->getId(), 'pple', 1, 15);

        // THEN
        $this->assertInstanceOf(DeckDetailsRead::class, $result);
        $this->assertCount(1, $result->getFlashcards());
        $this->assertInstanceOf(FlashcardRead::class, $result->getFlashcards()[0]);
        $this->assertSame($expected->id, $result->getFlashcards()[0]->getId()->getValue());
    }

    public function test__getByUser_ReturnOnlyUserCategories(): void
    {
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
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_deck->id, $results[0]->getId()->getValue());
        $this->assertSame($user_deck->name, $results[0]->getName());
        $this->assertSame($user_deck->default_language_level->value, $results[0]->getLanguageLevel()->value);
        $this->assertSame(FlashcardOwnerType::USER, $results[0]->getOwnerType());
    }

    public function test__getByUser_paginationWorks(): void
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
        $results = $this->repository->getByUser($user->getId(), null, 2, 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_deck->id, $results[0]->getId()->getValue());
    }

    public function test__getByUser_searchingWorks(): void
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
        $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($expected->id, $results[0]->getId()->getValue());
    }

    public function test__getAdminDecks_searchingWorks(): void
    {
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
        $results = $this->repository->getAdminDecks($user->getId(), 'LAn', 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($expected->id, $results[0]->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::ADMIN, $results[0]->getOwnerType());
    }

    public function test__getAdminDecks_lastLearntAtIsCorrect(): void
    {
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
        $results = $this->repository->getAdminDecks($user->getId(), 'LAn', 1, 15);

        // THEN
        $this->assertSame($now->toDateTimeString(), $results[0]->getLastLearntAt()->toDateTimeString());
    }

    public function test__getByUser_levelIsMostFrequentFlashcardLanguageLevel(): void
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
        $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame(LanguageLevel::A2, $results[0]->getLanguageLevel());
        $this->assertSame(6, $results[0]->getFlashcardsCount());
    }

    public function test__getByUser_ratingPercentageIsCorrect(): void
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
        ) / 3 * 100;

        // WHEN
        $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

        // THEN
        $this->assertSame(round($expected_ratio, 2), round($results[0]->getRatingPercentage(), 2));
    }

    public function test__getByUser_lastLearntAtIsCorrect(): void
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
        $results = $this->repository->getByUser($user->getId(), 'LAn', 1, 15);

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
