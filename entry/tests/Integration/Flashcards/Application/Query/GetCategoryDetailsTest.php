<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Query;

use Tests\TestCase;
use App\Models\Flashcard;
use App\Models\FlashcardCategory;
use Flashcard\Application\Query\GetCategoryDetails;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GetCategoryDetailsTest extends TestCase
{
    use DatabaseTransactions;

    private GetCategoryDetails $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = $this->app->make(GetCategoryDetails::class);
    }

    public function test__handle_ShouldReturnCategory(): void
    {
        // GIVEN
        $category = FlashcardCategory::factory()->create();
        Flashcard::factory()->create([
            'flashcard_category_id' => $category->id,
        ]);

        // WHEN
        $result = $this->query->get($category->getId(), null);

        // THEN
        $this->assertSame($category->id, $result->getId()->getValue());
        $this->assertSame($category->name, $result->getName());
    }
}
