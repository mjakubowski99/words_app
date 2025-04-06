<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\FlashcardPoll;

use Tests\TestCase;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Domain\Exceptions\FlashcardPollOverLoadedException;

class FlashcardPollTest extends TestCase
{
    public function test____construct_WhenPollSizeLowerThanLimit__success(): void
    {
        // GIVEN
        $user_id = \Mockery::mock(UserId::class);
        $poll_size = 2;
        $poll_limit = 3;

        // WHEN
        $poll = new FlashcardPoll($user_id, $poll_size, new FlashcardIdCollection([]), new FlashcardIdCollection([]), $poll_limit);

        // THEN
        $this->assertInstanceOf(FlashcardPoll::class, $poll);
    }

    public function test____construct_WhenPollSizeGreaterThanLimit__fail(): void
    {
        // GIVEN
        $user_id = \Mockery::mock(UserId::class);
        $poll_size = 5;
        $poll_limit = 3;

        // WHEN
        try {
            new FlashcardPoll($user_id, $poll_size, new FlashcardIdCollection([]), new FlashcardIdCollection([]), $poll_limit);
            $this->assertSame(1, 0, 'Failed to assert than exception thrown');
        } catch (FlashcardPollOverLoadedException $exception) {
            // THEN
            $this->assertSame($poll_limit, $exception->getExpectedMaxSize());
            $this->assertSame($poll_size, $exception->getCurrentSize());
        }
    }
}
