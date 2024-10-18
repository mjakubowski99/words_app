<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardCategoryReadRepository;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\FlashcardCategory;
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
        $result = $this->repository->findDetails($category->getId(), null);

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
        $results = $this->repository->getByOwner($user->toOwner(), 1, 15);

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
        $results = $this->repository->getByOwner($user->toOwner(), 2, 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OwnerCategoryRead::class, $results[0]);
        $this->assertSame($user_categories[1]->id, $results[0]->getId()->getValue());
    }
}
