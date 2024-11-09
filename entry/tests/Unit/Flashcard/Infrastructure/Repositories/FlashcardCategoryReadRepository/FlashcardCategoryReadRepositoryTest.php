<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardCategoryReadRepository;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardCategory;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\ReadModels\CategoryDetailsRead;
use Flashcard\Infrastructure\Repositories\FlashcardCategoryReadRepository;

class FlashcardCategoryReadRepositoryTest extends TestCase
{
    private FlashcardCategoryReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardCategoryReadRepository::class);
    }

    public function test__findDetails_success(): void
    {
        // GIVEN
        $category = FlashcardCategory::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'flashcard_category_id' => $category->id,
        ]);

        // WHEN
        $result = $this->repository->findDetails($category->getId(), null, 1, 15);

        // THEN
        $this->assertInstanceOf(CategoryDetailsRead::class, $result);
        $this->assertSame($category->getId()->getValue(), $result->getId()->getValue());
        $this->assertSame($category->name, $result->getName());
        $this->assertCount(1, $result->getFlashcards());
        $this->assertInstanceOf(FlashcardRead::class, $result->getFlashcards()[0]);
        $this->assertSame($flashcard->id, $result->getFlashcards()[0]->getId()->getValue());
        $this->assertSame($flashcard->word, $result->getFlashcards()[0]->getWord());
        $this->assertSame($flashcard->word_lang, $result->getFlashcards()[0]->getWordLang()->getValue());
        $this->assertSame($flashcard->translation, $result->getFlashcards()[0]->getTranslation());
        $this->assertSame($flashcard->translation_lang, $result->getFlashcards()[0]->getTranslationLang()->getValue());
        $this->assertSame($flashcard->context, $result->getFlashcards()[0]->getContext());
        $this->assertSame($flashcard->context_translation, $result->getFlashcards()[0]->getContextTranslation());
        $this->assertSame(GeneralRatingType::NEW, $result->getFlashcards()[0]->getGeneralRating()->getValue());
    }

    public function test__findDetails_generalRatingIsLastRating(): void
    {
        // GIVEN
        $category = FlashcardCategory::factory()->create();
        $flashcard = Flashcard::factory()->create([
            'flashcard_category_id' => $category->id,
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
        $result = $this->repository->findDetails($category->getId(), null, 1, 15);

        // THEN
        $this->assertInstanceOf(CategoryDetailsRead::class, $result);
        $this->assertSame($category->getId()->getValue(), $result->getId()->getValue());
        $this->assertSame($category->name, $result->getName());
        $this->assertCount(1, $result->getFlashcards());
        $this->assertSame(GeneralRatingType::GOOD, $result->getFlashcards()[0]->getGeneralRating()->getValue());
    }

    public function test__findDetails_searchWorks(): void
    {
        // GIVEN
        $category = FlashcardCategory::factory()->create();
        $other = Flashcard::factory()->create([
            'flashcard_category_id' => $category->id,
            'word' => 'Pen',
        ]);
        $expected = Flashcard::factory()->create([
            'flashcard_category_id' => $category->id,
            'word' => 'Apple',
        ]);

        // WHEN
        $result = $this->repository->findDetails($category->getId(), 'pple', 1, 15);

        // THEN
        $this->assertInstanceOf(CategoryDetailsRead::class, $result);
        $this->assertCount(1, $result->getFlashcards());
        $this->assertInstanceOf(FlashcardRead::class, $result->getFlashcards()[0]);
        $this->assertSame($expected->id, $result->getFlashcards()[0]->getId()->getValue());
    }

    public function test__getByOwner_ReturnOnlyUserCategories(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $other_category = FlashcardCategory::factory()->create();
        $user_category = FlashcardCategory::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), null, 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_category->id, $results[0]->getId()->getValue());
        $this->assertSame($user_category->name, $results[0]->getName());
    }

    public function test__getByOwner_paginationWorks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $user_categories = FlashcardCategory::factory(2)->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), null, 2, 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_categories[1]->id, $results[0]->getId()->getValue());
    }

    public function test__getByOwner_searchingWorks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $other = FlashcardCategory::factory()->create([
            'user_id' => $user->id,
            'name' => 'Nal',
        ]);
        $expected = FlashcardCategory::factory()->create([
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
}
