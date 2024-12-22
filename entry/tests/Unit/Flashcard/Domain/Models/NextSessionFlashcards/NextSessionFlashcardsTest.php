<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\NextSessionFlashcards;

use Tests\TestCase;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\Uuid;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\NextSessionFlashcards;

class NextSessionFlashcardsTest extends TestCase
{
    use NextSessionFlashcardsTrait;

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
            $owner,
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
            $owner,
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
            $owner,
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
}
