<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\GetRandomFlashcards;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Base\FlashcardTestCase;

class FlashcardsRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
    }

    public function test__getRandomFlashcards_returnUserFlashcards(): void
    {
        // GIVEN
        $owner = $this->createUser();
        $other_flashcard = $this->createFlashcard();
        $flashcard = $this->createFlashcard(['user_id' => $owner->id]);

        // WHEN
        $flashcards = $this->repository->getRandomFlashcards($owner->toOwner(), 5, []);

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $this->assertSame($flashcard->getId()->getValue(), $flashcards[0]->getId()->getValue());
    }

    public function test__getRandomFlashcards_LimitWorks(): void
    {
        // GIVEN
        $owner = $this->createUser();
        $other_flashcard = $this->createFlashcard();
        $this->createFlashcard(['user_id' => $owner->id]);
        $this->createFlashcard(['user_id' => $owner->id]);

        // WHEN
        $flashcards = $this->repository->getRandomFlashcards($owner->toOwner(), 1, []);

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
    }

    public function test__getRandomFlashcards_ExcludeFlashcardIds(): void
    {
        // GIVEN
        $owner = $this->createUser();
        $flashcards = [
            $this->createFlashcard(['user_id' => $owner->id]),
            $this->createFlashcard(['user_id' => $owner->id]),
            $this->createFlashcard(['user_id' => $owner->id]),
        ];
        $expected_flashcard = $flashcards[1];
        $flashcards_to_exclude = [$flashcards[0]->id, $flashcards[2]->id];

        // WHEN
        $flashcards = $this->repository->getRandomFlashcards($owner->toOwner(), 100, $flashcards_to_exclude);

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(Flashcard::class, $flashcards[0]);
        $this->assertSame($expected_flashcard->id, $flashcards[0]->getId()->getValue());
    }
}
