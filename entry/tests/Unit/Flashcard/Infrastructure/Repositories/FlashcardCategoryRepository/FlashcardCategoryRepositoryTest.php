<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardCategoryRepository;

use Tests\TestCase;
use App\Models\User;
use App\Models\FlashcardCategory;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Infrastructure\Repositories\FlashcardCategoryRepository;

class FlashcardCategoryRepositoryTest extends TestCase
{
    private FlashcardCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardCategoryRepository::class);
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
        $this->assertSame($category->user->getId()->getValue(), $result->getOwner()->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::USER, $result->getOwner()->getOwnerType());
    }

    public function test__createCategory_ShouldCreateCategory(): void
    {
        // GIVEN
        $category = \Mockery::mock(Category::class);
        $user = User::factory()->create();
        $category->allows([
            'getName' => 'Cat name',
            'hasOwner' => true,
            'getOwner' => $user->toOwner(),
        ]);

        // WHEN
        $this->repository->createCategory($category);

        // THEN
        $this->assertDatabaseHas('flashcard_categories', [
            'name' => 'Cat name',
            'user_id' => $user->id,
        ]);
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

    public function test__searchByName_shouldReturnUserCategory(): void
    {
        // GIVEN
        $user = User::factory()->create();
        FlashcardCategory::factory()->create(['name' => 'Category']);
        $expected_category = FlashcardCategory::factory()->create(['name' => 'Category', 'user_id' => $user->id]);
        $owner = new Owner(new OwnerId($user->id), FlashcardOwnerType::USER);

        // WHEN
        $category = $this->repository->searchByName($owner, 'category');

        // THEN
        $this->assertSame($expected_category->id, $category->getId()->getValue());
        $this->assertSame($expected_category->name, $category->getName());
        $this->assertSame($user->id, $category->getOwner()->getId()->getValue());
    }

    public function test__searchByName_shouldCorrectlySearchByNAME(): void
    {
        // GIVEN
        $user = User::factory()->create();
        FlashcardCategory::factory()->create(['name' => 'Category 1', 'user_id' => $user->id]);
        $expected_category = FlashcardCategory::factory()->create(['name' => 'Category', 'user_id' => $user->id]);
        $owner = new Owner(new OwnerId($user->id), FlashcardOwnerType::USER);

        // WHEN
        $category = $this->repository->searchByName($owner, 'category');

        // THEN
        $this->assertSame($expected_category->id, $category->getId()->getValue());
        $this->assertSame($expected_category->name, $category->getName());
    }
}
