<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\SmTwoFlashcards;

use Flashcard\Domain\Exceptions\InvalidSmTwoFlashcardSetException;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Shared\Enum\FlashcardOwnerType;
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
        $user_id = UserId::fromString(Uuid::make()->getValue());
        $other_user_id = UserId::fromString(Uuid::make()->getValue());
        $flashcard_id = new FlashcardId(1);
        $sm_two_flashcards = [
            $this->makeSmTwoFlashcard($other_user_id, $flashcard_id),
            $this->makeSmTwoFlashcard($user_id, $flashcard_id),
        ];

        // THEN
        $this->expectException(InvalidSmTwoFlashcardSetException::class);

        // WHEN
        new SmTwoFlashcards($sm_two_flashcards);
    }

    private function makeSmTwoFlashcard(UserId $user_id, FlashcardId $flashcard_id): SmTwoFlashcard
    {
        $onwer = new Owner(new OwnerId($user_id->getValue()), FlashcardOwnerType::USER);

        return new SmTwoFlashcard($onwer, $flashcard_id, null, null, null);
    }
}
