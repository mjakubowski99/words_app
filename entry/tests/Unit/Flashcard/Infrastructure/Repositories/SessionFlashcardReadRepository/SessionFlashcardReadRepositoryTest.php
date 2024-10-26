<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\SessionFlashcardReadRepository;

use Tests\TestCase;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Rating;
use App\Models\LearningSessionFlashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\SessionFlashcardReadRepository;

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
        $this->assertSame($expected->flashcard->translation, $session_flashcards[0]->getTranslation());
        $this->assertSame($expected->flashcard->word_lang, $session_flashcards[0]->getWordLang()->getValue());
        $this->assertSame($expected->flashcard->translation_lang, $session_flashcards[0]->getTranslationLang()->getValue());
        $this->assertSame($expected->flashcard->translation, $session_flashcards[0]->getTranslation());
        $this->assertSame($expected->flashcard->context, $session_flashcards[0]->getContext());
        $this->assertSame($expected->flashcard->context_translation, $session_flashcards[0]->getContextTranslation());
    }
}
