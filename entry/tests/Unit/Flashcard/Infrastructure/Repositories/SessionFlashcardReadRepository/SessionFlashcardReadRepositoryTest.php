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
        $results = $this->repository->findUnratedById($session->getId(), 5);

        // THEN
        $this->assertCount(1, $results);
        $this->assertSame($expected->id, $results[0]->getId()->getValue());
        $this->assertSame($expected->flashcard->translation, $results[0]->getTranslation());
        $this->assertSame($expected->flashcard->word_lang, $results[0]->getWordLang()->getValue());
        $this->assertSame($expected->flashcard->translation_lang, $results[0]->getTranslationLang()->getValue());
        $this->assertSame($expected->flashcard->translation, $results[0]->getTranslation());
        $this->assertSame($expected->flashcard->context, $results[0]->getContext());
        $this->assertSame($expected->flashcard->context_translation, $results[0]->getContextTranslation());
    }
}
