<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Tests\Base\FlashcardTestCase;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;

class FlashcardDeckReadRepositoryTest extends FlashcardTestCase
{
    private FlashcardDeckReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardDeckReadRepository::class);
    }

    public function test__findDetails_success(): void
    {
        // GIVEN
        $deck = FlashcardDeck::factory()->create();
        $flashcard = Flashcard::factory()->create([
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
    }

    public function test__findDetails_generalRatingIsLastRating(): void
    {
        // GIVEN
        $deck = FlashcardDeck::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'flashcard_deck_id' => $deck->id,
        ]);
        LearningSessionFlashcard::factory()->create([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::WEAK,
            'updated_at' => now()->subMinute(),
        ]);
        LearningSessionFlashcard::factory()->create([
            'flashcard_id' => $flashcard->id,
            'rating' => Rating::GOOD,
            'updated_at' => now(),
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
        $deck = FlashcardDeck::factory()->create();
        $other = Flashcard::factory()->create([
            'flashcard_deck_id' => $deck->id,
            'front_word' => 'Pen',
        ]);
        $expected = Flashcard::factory()->create([
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
        $user = User::factory()->create();
        $other_deck = FlashcardDeck::factory()->create();
        $user_deck = FlashcardDeck::factory()->create([
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
        $user = User::factory()->create();
        $user_decks = FlashcardDeck::factory(2)->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), null, 2, 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_decks[1]->id, $results[0]->getId()->getValue());
    }

    public function test__getByOwner_searchingWorks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $other = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
            'name' => 'Nal',
        ]);
        $expected = FlashcardDeck::factory()->create([
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
        $user = User::factory()->create();
        $expected = FlashcardDeck::factory()->create([
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
    }
}
