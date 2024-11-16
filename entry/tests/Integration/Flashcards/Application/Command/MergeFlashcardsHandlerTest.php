<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Command;

use Tests\Base\FlashcardTestCase;
use Shared\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Command\MergeFlashcardCategoriesHandler;

class MergeFlashcardsHandlerTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private MergeFlashcardCategoriesHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->app->make(MergeFlashcardCategoriesHandler::class);
    }

    /**
     * @test
     */
    public function handle_WhenUserIsCategoryOwner_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_category = $this->createFlashcardCategory(['user_id' => $user->id]);
        $to_category = $this->createFlashcardCategory(['user_id' => $user->id]);
        $from_flashcard = $this->createFlashcard(['flashcard_category_id' => $to_category->id]);
        $to_flashcard = $this->createFlashcard(['flashcard_category_id' => $to_category->id]);

        // WHEN
        $result = $this->handler->handle(
            $user->toOwner(),
            $from_category->getId(),
            $to_category->getId(),
            'New name'
        );

        // THEN
        $this->assertTrue($result);
        $this->assertDatabaseHas('flashcards', [
            'id' => $from_flashcard->id,
            'flashcard_category_id' => $to_category->id,
        ]);
        $this->assertDatabaseHas('flashcards', [
            'id' => $to_flashcard->id,
            'flashcard_category_id' => $to_category->id,
        ]);
        $this->assertDatabaseHas('flashcard_categories', [
            'id' => $to_category->id,
            'name' => 'New name',
        ]);
        $this->assertDatabaseMissing('flashcard_categories', ['id' => $from_category->id]);
    }

    /**
     * @test
     */
    public function handle_WhenCategoryHasSessions_success(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_category = $this->createFlashcardCategory(['user_id' => $user->id]);
        $to_category = $this->createFlashcardCategory(['user_id' => $user->id]);
        $from_learning_session = $this->createLearningSession([
            'flashcard_category_id' => $from_category->id,
        ]);

        // WHEN
        $result = $this->handler->handle($user->toOwner(), $from_category->getId(), $to_category->getId(), 'New name');

        // THEN
        $this->assertTrue($result);
        $this->assertDatabaseHas('learning_sessions', [
            'id' => $from_learning_session->id,
            'flashcard_category_id' => $to_category->id,
        ]);
    }

    /**
     * @test
     */
    public function handle_WhenUserIsNotToCategoryOwner_fail(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_category = $this->createFlashcardCategory(['user_id' => $user->id]);
        $to_category = $this->createFlashcardCategory();

        // THEN
        $this->expectException(UnauthorizedException::class);

        // WHEN
        $this->handler->handle($user->toOwner(), $from_category->getId(), $to_category->getId(), 'New name');
    }

    /**
     * @test
     */
    public function handle_WhenUserIsNotFromCategoryOwner_fail(): void
    {
        // GIVEN
        $user = $this->createUser();
        $from_category = $this->createFlashcardCategory();
        $to_category = $this->createFlashcardCategory(['user_id' => $user->id]);

        // THEN
        $this->expectException(UnauthorizedException::class);

        // WHEN
        $this->handler->handle($user->toOwner(), $from_category->getId(), $to_category->getId(), 'New name');
    }
}
