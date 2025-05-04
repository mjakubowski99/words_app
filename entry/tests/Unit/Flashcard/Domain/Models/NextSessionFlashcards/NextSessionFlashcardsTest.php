<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\NextSessionFlashcards;

use Tests\TestCase;
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

class NextSessionFlashcardsTest extends TestCase
{
    private NextSessionFlashcards $model;

    public function test__construct_WhenFlashcardsCountGreaterThanMax_fail(): void
    {
        // GIVEN
        $owner = $this->makeOwner();

        // THEN
        $this->expectException(\Exception::class);

        // WHEN
        new NextSessionFlashcards(
            new SessionId(1),
            SessionType::FLASHCARD,
            UserId::fromString($owner->getId()->getValue()),
            $this->makeCategory($owner),
            12,
            10,
            11,
        );
    }

    public function test__addNext_WhenFlashcardLimitNotExceeded_success(): void
    {
        // GIVEN
        $owner = $this->makeOwner();
        $model = new NextSessionFlashcards(
            new SessionId(1),
            SessionType::FLASHCARD,
            UserId::fromString($owner->getId()->getValue()),
            $this->makeCategory($owner),
            10,
            3,
            11,
        );
        $flashcard = $this->makeFlashcard($owner);

        // WHEN
        $model->addNext($flashcard);

        // THEN
        $this->assertCount(1, $model->getNextFlashcards());
        $this->assertTrue($flashcard->getId()->equals($model->getNextFlashcards()[0]->getFlashcardId()));
    }

    public function test__addNext_WhenFlashcardLimitExceeded_fail(): void
    {
        // GIVEN
        $owner = $this->makeOwner();
        $model = new NextSessionFlashcards(
            new SessionId(1),
            SessionType::FLASHCARD,
            UserId::fromString($owner->getId()->getValue()),
            $this->makeCategory($owner),
            11,
            3,
            11,
        );
        $flashcard = $this->makeFlashcard($owner);

        // THEN
        $this->expectException(\Exception::class);

        // WHEN
        $model->addNext($flashcard);
    }

    /** @dataProvider exerciseTypeProvider */
    public function test__resolveExerciseType_WhenNotMixedType_success(SessionType $type, ?ExerciseType $expected_type): void
    {
        // GIVEN
        $owner = $this->makeOwner();
        $model = new NextSessionFlashcards(
            new SessionId(1),
            $type,
            UserId::fromString($owner->getId()->getValue()),
            $this->makeCategory($owner),
            11,
            3,
            11,
        );

        // WHEN
        $result = $model->resolveNextExerciseType();

        // THEN
        $this->assertSame($result, $expected_type);
    }

    public static function exerciseTypeProvider(): array
    {
        return [
            ['type' => SessionType::FLASHCARD, 'expected_type' => null],
            ['type' => SessionType::UNSCRAMBLE_WORDS, 'expected_type' => ExerciseType::UNSCRAMBLE_WORDS],
        ];
    }

    public function test__resolveExerciseType_WhenMixedType_randomChoice(): void
    {
        // GIVEN
        $owner = $this->makeOwner();
        $model = new NextSessionFlashcards(
            new SessionId(1),
            SessionType::MIXED,
            UserId::fromString($owner->getId()->getValue()),
            $this->makeCategory($owner),
            11,
            3,
            11,
        );

        // WHEN
        $result = $model->resolveNextExerciseType();

        // THEN
        $this->assertTrue(in_array($result, [ExerciseType::UNSCRAMBLE_WORDS, null]));
    }

    private function makeCategory(Owner $owner): Deck
    {
        return new Deck($owner, 'tag', 'name', LanguageLevel::A2);
    }

    private function makeOwner(): Owner
    {
        return new Owner(new OwnerId(Uuid::make()->getValue()), FlashcardOwnerType::USER);
    }

    private function makeFlashcard(Owner $owner): Flashcard
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
}
