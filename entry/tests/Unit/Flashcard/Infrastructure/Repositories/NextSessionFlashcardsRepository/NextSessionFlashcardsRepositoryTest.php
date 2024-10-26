<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\NextSessionFlashcardsRepository;

use Tests\TestCase;
use App\Models\Flashcard;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\NextSessionFlashcardsRepository;

class NextSessionFlashcardsRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private NextSessionFlashcardsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(NextSessionFlashcardsRepository::class);
    }

    public function test__find_NoFlashcards_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame(0, $result->getUnratedCount());
        $this->assertSame(0, $result->getCurrentSessionFlashcardsCount());
    }

    public function test__find_HasFlashcards_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame(1, $result->getUnratedCount());
        $this->assertSame(2, $result->getCurrentSessionFlashcardsCount());
    }

    public function test__find_WhenNoCategory_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create([
            'flashcard_category_id' => null,
        ]);
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame($session->user_id, $result->getOwner()->getId()->getValue());
        $this->assertFalse($result->hasCategory());
    }

    public function test__find_MultipleCategories_ShouldFindCorrectObject(): void
    {
        // GIVEN
        $categories = FlashcardCategory::factory(2)->create();
        $session = LearningSession::factory()->create([
            'flashcard_category_id' => $categories[1]->id,
        ]);

        // WHEN
        $result = $this->repository->find($session->getId());

        // THEN
        $this->assertSame($session->id, $result->getSessionId()->getValue());
        $this->assertSame($categories[1]->id, $result->getCategory()->getId()->getValue());
        $this->assertSame($categories[1]->name, $result->getCategory()->getName());
        $this->assertSame($categories[1]->tag, $result->getCategory()->getTag());
    }

    public function test__save_ShouldSaveObject(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $flashcard = Flashcard::factory()->create();
        $object = new NextSessionFlashcards(
            $session->getId(),
            $session->user->toOwner(),
            $session->category->toDomainModel(),
            8,
            2,
            10
        );
        $object->addNext($flashcard->toDomainModel());

        // WHEN
        $this->repository->save($object);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'learning_session_id' => $session->id,
            'flashcard_id' => $flashcard->id,
            'rating' => null,
        ]);
    }
}
