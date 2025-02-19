<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\GetByDeck;

use App\Models\Admin;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Flashcard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

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
    public function getByDeck_returnCorrectData(): void
    {
        // GIVEN
        $deck = $this->createFlashcardDeck();
        $expected_flashcard = $this->createFlashcard(['flashcard_deck_id' => $deck->id]);

        // WHEN
        $flashcards = $this->repository->getByDeck($deck->getId());

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $flashcard = $flashcards[0];
        $this->assertSame($expected_flashcard->id, $flashcard->getId()->getValue());
        $this->assertSame($expected_flashcard->front_word, $flashcard->getFrontWord());
        $this->assertSame($expected_flashcard->back_word, $flashcard->getBackWord());
        $this->assertSame($expected_flashcard->back_lang, $flashcard->getBackLang()->getValue());
        $this->assertSame($expected_flashcard->front_lang, $flashcard->getFrontLang()->getValue());
        $this->assertSame($expected_flashcard->back_context, $flashcard->getBackContext());
        $this->assertSame($expected_flashcard->front_context, $flashcard->getFrontContext());
        $this->assertSame($expected_flashcard->emoji, $flashcard->getEmoji()->toUnicode());
    }

    /**
     * @test
     */
    public function getByDeck_adminIsOwner(): void
    {
        // GIVEN
        $deck = $this->createFlashcardDeck();
        $admin = Admin::factory()->create();
        $expected_flashcard = $this->createFlashcard([
            'flashcard_deck_id' => $deck->id,
            'admin_id' => $admin->id,
            'user_id' => null,
        ]);

        // WHEN
        $flashcards = $this->repository->getByDeck($deck->getId());

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $flashcard = $flashcards[0];
        $this->assertTrue($flashcard->getOwner()->isAdmin());
        $this->assertSame($expected_flashcard->admin_id, $flashcard->getOwner()->getId()->getValue());
    }

    /**
     * @test
     */
    public function getByDeck_returnOnlyFlashcardsForGivenDeck(): void
    {
        // GIVEN
        $deck = $this->createFlashcardDeck();
        $other_flashcard = $this->createFlashcard();
        $flashcard = $this->createFlashcard(['flashcard_deck_id' => $deck->id]);

        // WHEN
        $flashcards = $this->repository->getByDeck($deck->getId());

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $this->assertSame($flashcard->getId()->getValue(), $flashcards[0]->getId()->getValue());
    }
}
