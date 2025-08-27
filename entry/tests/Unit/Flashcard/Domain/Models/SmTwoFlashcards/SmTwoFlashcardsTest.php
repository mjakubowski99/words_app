<?php

declare(strict_types=1);

use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Exceptions\InvalidSmTwoFlashcardSetException;

test('construct when not every user in set has same id should throw exception', function () {
    // GIVEN
    $user_id = UserId::fromString(Uuid::make()->getValue());
    $other_user_id = UserId::fromString(Uuid::make()->getValue());
    $flashcard_id = new FlashcardId(1);
    $sm_two_flashcards = [
        makeSmTwoFlashcard($other_user_id, $flashcard_id),
        makeSmTwoFlashcard($user_id, $flashcard_id),
    ];

    // THEN
    $this->expectException(InvalidSmTwoFlashcardSetException::class);

    // WHEN
    new SmTwoFlashcards($sm_two_flashcards);
});

function makeSmTwoFlashcard(UserId $user_id, FlashcardId $flashcard_id): SmTwoFlashcard
{
    return new SmTwoFlashcard($user_id, $flashcard_id, null, null, null);
}
