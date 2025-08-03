<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\SessionFlashcardReadRepository;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Flashcard;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use Shared\Enum\FlashcardOwnerType;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\SessionFlashcardReadRepository;

class SessionFlashcardReadRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private SessionFlashcardReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(SessionFlashcardReadRepository::class);
    }

    public function test__findUnratedById_ShouldReturnOnlyUnratedFlashcards(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD->value,
        ]);
        $expected = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->findUnratedById($session->getId(), 5);
        $session_flashcards = $result->getSessionFlashcards();

        // THEN
        $this->assertCount(1, $result->getSessionFlashcards());
        $this->assertSame($expected->id, $session_flashcards[0]->getId()->getValue());
        $this->assertSame($expected->flashcard->front_word, $session_flashcards[0]->getFrontWord());
        $this->assertSame($expected->flashcard->front_lang, $session_flashcards[0]->getFrontLang()->getValue());
        $this->assertSame($expected->flashcard->back_lang, $session_flashcards[0]->getBackLang()->getValue());
        $this->assertSame($expected->flashcard->back_word, $session_flashcards[0]->getBackWord());
        $this->assertSame($expected->flashcard->front_context, $session_flashcards[0]->getFrontContext());
        $this->assertSame($expected->flashcard->back_context, $session_flashcards[0]->getBackContext());
        $this->assertSame($expected->flashcard->language_level, $session_flashcards[0]->getLanguageLevel()->value);
        $this->assertSame($expected->flashcard->emoji, $session_flashcards[0]->getEmoji()->toUnicode());
        $this->assertSame(FlashcardOwnerType::USER, $session_flashcards[0]->getOwnerType());
    }

    public function test__findUnratedById_WhenNoUnratedFlashcard_correctExerciseMode(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'rating' => Rating::GOOD->value,
            'exercise_entry_id' => 1,
        ]);

        // WHEN
        $result = $this->repository->findUnratedById($session->getId(), 5);

        // THEN
        $this->assertCount(0, $result->getSessionFlashcards());
        $this->assertCount(0, $result->getExerciseSummaries());
        $this->assertTrue($result->isExerciseMode());
    }

    public function test__findUnratedById_AdminIsOwner(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $flashcard = Flashcard::factory()->byAdmin(Admin::factory()->create())->create();
        LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
            'flashcard_id' => $flashcard->id,
            'rating' => null,
        ]);

        // WHEN
        $result = $this->repository->findUnratedById($session->getId(), 5);
        $session_flashcards = $result->getSessionFlashcards();

        // THEN
        $this->assertCount(1, $result->getSessionFlashcards());
        $this->assertSame(FlashcardOwnerType::ADMIN, $session_flashcards[0]->getOwnerType());
    }
}
