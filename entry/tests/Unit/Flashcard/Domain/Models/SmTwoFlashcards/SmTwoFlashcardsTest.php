<?php

namespace Tests\Unit\Flashcard\Domain\Models\SmTwoFlashcards;

use Flashcard\Domain\Exceptions\InvalidSmTwoFlashcardSetException;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Uuid;
use Tests\TestCase;

class SmTwoFlashcardsTest extends TestCase
{
    /**
     * @test
     */
    public function construct_WhenNotEveryUserInSetHasSameId_ShouldThrowException(): void
    {
        // GIVEN
        $user_id = UserId::fromString(Uuid::make());
        $other_user_id = UserId::fromString(Uuid::make());
        $flashcard_id = new FlashcardId('1');
        $sm_two_flashcards = [
            $this->makeSmTwoFlashcard($other_user_id, $flashcard_id),
            $this->makeSmTwoFlashcard($user_id, $flashcard_id),
        ];

        // THEN
        $this->expectException(InvalidSmTwoFlashcardSetException::class);

        // WHEN
        new SmTwoFlashcards($sm_two_flashcards);
    }

    /**
     * @test
     */
    public function searchKeyByUserFlashcard_ShouldReturnCorrectKey(): void
    {
        // GIVEN
        $user_id = UserId::fromString(Uuid::make());
        $flashcard_id = new FlashcardId('1');
        $other_flashcard_id = new FlashcardId('2');

        $sm_two_flashcards = [
            $this->makeSmTwoFlashcard($user_id, $other_flashcard_id),
            $this->makeSmTwoFlashcard($user_id, $flashcard_id),
        ];
        $sm_two_flashcards = new SmTwoFlashcards($sm_two_flashcards);

        // WHEN
        $key = $sm_two_flashcards->searchKeyByUserFlashcard($flashcard_id);

        // THEN
        $this->assertSame(1, $key);
    }

    private function makeSmTwoFlashcard(UserId $user_id, FlashcardId $flashcard_id): SmTwoFlashcard
    {
        $flashcard = new Flashcard(
            $flashcard_id,
            'word',
            Language::from(Language::EN),
            'translation',
            Language::from(Language::PL),
            'context',
            'context translation',
        );
        return new SmTwoFlashcard($user_id, $flashcard, null, null, null);
    }
}
