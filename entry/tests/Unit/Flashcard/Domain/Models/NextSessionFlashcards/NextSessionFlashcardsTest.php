<?php

declare(strict_types=1);

use Shared\Enum\SessionType;
use Shared\Enum\ExerciseType;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\NextSessionFlashcards;

test('construct when flashcards count greater than max fail', function () {
    // GIVEN
    $owner = makeOwner();

    // THEN
    $this->expectException(Exception::class);

    // WHEN
    new NextSessionFlashcards(
        new SessionId(1),
        SessionType::FLASHCARD,
        UserId::fromString($owner->getId()->getValue()),
        makeCategory($owner),
        12,
        10,
        11,
    );
});
test('add next when flashcard limit not exceeded success', function () {
    // GIVEN
    $owner = makeOwner();
    $model = new NextSessionFlashcards(
        new SessionId(1),
        SessionType::FLASHCARD,
        UserId::fromString($owner->getId()->getValue()),
        makeCategory($owner),
        10,
        3,
        11,
    );
    $flashcard = makeFlashcard($owner);

    // WHEN
    $model->addNext($flashcard);

    // THEN
    expect($model->getNextFlashcards())->toHaveCount(1);
    expect($flashcard->getId()->equals($model->getNextFlashcards()[0]->getFlashcardId()))->toBeTrue();
});
test('add next when flashcard limit exceeded fail', function () {
    // GIVEN
    $owner = makeOwner();
    $model = new NextSessionFlashcards(
        new SessionId(1),
        SessionType::FLASHCARD,
        UserId::fromString($owner->getId()->getValue()),
        makeCategory($owner),
        11,
        3,
        11,
    );
    $flashcard = makeFlashcard($owner);

    // THEN
    $this->expectException(Exception::class);

    // WHEN
    $model->addNext($flashcard);
});
test('resolve exercise type when not mixed type success', function (SessionType $type, ?ExerciseType $expected_type) {
    // GIVEN
    $owner = makeOwner();
    $model = new NextSessionFlashcards(
        new SessionId(1),
        $type,
        UserId::fromString($owner->getId()->getValue()),
        makeCategory($owner),
        11,
        3,
        11,
    );

    // WHEN
    $result = $model->resolveNextExerciseType();

    // THEN
    expect($expected_type)->toBe($result);
})->with('exerciseTypeProvider');

dataset('exerciseTypeProvider', [
    ['type' => SessionType::FLASHCARD, 'expected_type' => null],
    ['type' => SessionType::UNSCRAMBLE_WORDS, 'expected_type' => ExerciseType::UNSCRAMBLE_WORDS],
]);

test('resolve exercise type when mixed type random choice', function () {
    // GIVEN
    $owner = makeOwner();
    $model = new NextSessionFlashcards(
        new SessionId(1),
        SessionType::MIXED,
        UserId::fromString($owner->getId()->getValue()),
        makeCategory($owner),
        11,
        3,
        11,
    );

    // WHEN
    $result = $model->resolveNextExerciseType();

    // THEN
    expect(in_array($result, [ExerciseType::UNSCRAMBLE_WORDS, null]))->toBeTrue();
});

function makeCategory(Owner $owner): Deck
{
    return new Deck($owner, 'tag', 'name', LanguageLevel::A2);
}

function makeOwner(): Owner
{
    return new Owner(new OwnerId(Uuid::make()->getValue()), FlashcardOwnerType::USER);
}

function makeFlashcard(Owner $owner): Flashcard
{
    return new Flashcard(
        new FlashcardId(1),
        'word',
        Language::pl(),
        'trans',
        Language::en(),
        'context',
        'context_translation',
        $owner,
        null,
        LanguageLevel::A1,
    );
}
