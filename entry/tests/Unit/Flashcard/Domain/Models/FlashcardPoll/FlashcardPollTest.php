<?php

declare(strict_types=1);
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Domain\Exceptions\FlashcardPollOverLoadedException;

test('construct when poll size lower than limit  success', function () {
    // GIVEN
    $user_id = Mockery::mock(UserId::class);
    $poll_size = 2;
    $poll_limit = 3;

    // WHEN
    $poll = new FlashcardPoll($user_id, $poll_size, new FlashcardIdCollection([]), new FlashcardIdCollection([]), $poll_limit);

    // THEN
    expect($poll)->toBeInstanceOf(FlashcardPoll::class);
});
test('construct when poll size greater than limit  fail', function () {
    // GIVEN
    $user_id = Mockery::mock(UserId::class);
    $poll_size = 5;
    $poll_limit = 3;

    // WHEN
    try {
        new FlashcardPoll($user_id, $poll_size, new FlashcardIdCollection([]), new FlashcardIdCollection([]), $poll_limit);
        expect(0)->toBe(1, 'Failed to assert than exception thrown');
    } catch (FlashcardPollOverLoadedException $exception) {
        // THEN
        expect($exception->getExpectedMaxSize())->toBe($poll_limit);
        expect($exception->getCurrentSize())->toBe($poll_size);
    }
});
