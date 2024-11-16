<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardRepository\GetByCategory;

use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Flashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\FlashcardRepository;

class FlashcardRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
    }

    /**
     * @test
     */
    public function getByCategory_returnCorrectData(): void
    {
        // GIVEN
        $category = $this->createFlashcardCategory();
        $expected_flashcard = $this->createFlashcard(['flashcard_category_id' => $category->id]);

        // WHEN
        $flashcards = $this->repository->getByCategory($category->getId());

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $flashcard = $flashcards[0];
        $this->assertSame($expected_flashcard->id, $flashcard->getId()->getValue());
        $this->assertSame($expected_flashcard->word, $flashcard->getWord());
        $this->assertSame($expected_flashcard->translation, $flashcard->getTranslation());
        $this->assertSame($expected_flashcard->translation_lang, $flashcard->getTranslationLang()->getValue());
        $this->assertSame($expected_flashcard->word_lang, $flashcard->getWordLang()->getValue());
        $this->assertSame($expected_flashcard->context_translation, $flashcard->getContextTranslation());
        $this->assertSame($expected_flashcard->context, $flashcard->getContext());
    }

    /**
     * @test
     */
    public function getByCategory_returnOnlyFlashcardsForGivenCategory(): void
    {
        // GIVEN
        $category = $this->createFlashcardCategory();
        $other_flashcard = $this->createFlashcard();
        $flashcard = $this->createFlashcard(['flashcard_category_id' => $category->id]);

        // WHEN
        $flashcards = $this->repository->getByCategory($category->getId());

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $this->assertSame($flashcard->getId()->getValue(), $flashcards[0]->getId()->getValue());
    }
}
