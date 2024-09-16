<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Query;

use Tests\TestCase;
use Flashcard\Application\Query\GetMainCategory;
use Flashcard\Application\DTO\MainFlashcardCategoryDTO;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GetMainCategoryTest extends TestCase
{
    use DatabaseTransactions;

    private GetMainCategory $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = $this->app->make(GetMainCategory::class);
    }

    public function test__get_ShouldReturnMainCategory(): void
    {
        // GIVEN
        // WHEN
        $result = $this->query->handle();

        // THEN
        $this->assertInstanceOf(MainFlashcardCategoryDTO::class, $result);
        $this->assertSame(0, $result->getId()->getValue());
        $this->assertSame(__('Main category'), $result->getName());
    }
}
