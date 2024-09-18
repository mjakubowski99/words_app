<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardCategoryRepository;

use Tests\TestCase;
use App\Models\User;
use App\Models\FlashcardCategory;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Category;
use Shared\Enum\FlashcardCategoryType;
use Flashcard\Domain\Contracts\ICategory;
use Flashcard\Domain\Models\MainCategory;
use Flashcard\Domain\Exceptions\CannotCreateCategoryException;
use Flashcard\Infrastructure\Repositories\FlashcardCategoryRepository;

class FlashcardCategoryRepositoryTest extends TestCase
{
    private FlashcardCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardCategoryRepository::class);
    }

    public function test__findById_WhenMainCategory_success(): void
    {
        // GIVEN
        $category_id = new MainCategory();

        // WHEN
        $result = $this->repository->findById($category_id->getId());

        // THEN
        $this->assertInstanceOf(MainCategory::class, $result);
    }

    public function test__findById_WhenNormalCategory_success(): void
    {
        // GIVEN
        $other_category = FlashcardCategory::factory()->create();
        $category = FlashcardCategory::factory()->create();

        // WHEN
        $result = $this->repository->findById($category->getId());

        // THEN
        $this->assertInstanceOf(Category::class, $result);
        $this->assertSame($category->getId()->getValue(), $result->getId()->getValue());
        $this->assertSame($category->name, $result->getName());
        $this->assertSame(FlashcardCategoryType::NORMAL, $result->getCategoryType());
        $this->assertSame($category->user->getId()->getValue(), $result->getOwner()->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::USER, $result->getOwner()->getOwnerType());
    }

    public function test__createCategory_ShouldCreateCategory(): void
    {
        // GIVEN
        $category = \Mockery::mock(ICategory::class);
        $user = User::factory()->create();
        $category->allows([
            'getName' => 'Cat name',
            'hasOwner' => true,
            'getOwner' => $user->toOwner(),
            'getCategoryType' => FlashcardCategoryType::NORMAL,
        ]);

        // WHEN
        $this->repository->createCategory($category);

        // THEN
        $this->assertDatabaseHas('flashcard_categories', [
            'name' => 'Cat name',
            'user_id' => $user->id,
        ]);
    }

    public function test__createCategory_WhenMainCategory_fail(): void
    {
        // GIVEN
        $category = \Mockery::mock(ICategory::class);
        $user = User::factory()->create();
        $category->allows([
            'getName' => 'Cat name',
            'hasOwner' => false,
            'getOwner' => null,
            'getCategoryType' => FlashcardCategoryType::GENERAL,
        ]);

        // THEN
        $this->expectException(CannotCreateCategoryException::class);

        // WHEN
        $this->repository->createCategory($category);
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
        $this->assertInstanceOf(Category::class, $results[0]);
        $this->assertSame($user_category->id, $results[0]->getId()->getValue());
        $this->assertSame($user_category->name, $results[0]->getName());
        $this->assertSame($user_category->user_id, $results[0]->getOwner()->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::USER, $results[0]->getOwner()->getOwnerType());
        $this->assertSame(FlashcardCategoryType::NORMAL, $results[0]->getCategoryType());
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
        $this->assertInstanceOf(Category::class, $results[0]);
        $this->assertSame($user_categories[1]->id, $results[0]->getId()->getValue());
    }
}
