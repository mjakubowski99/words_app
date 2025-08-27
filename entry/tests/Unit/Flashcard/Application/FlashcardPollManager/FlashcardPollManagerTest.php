<?php

declare(strict_types=1);
use Ramsey\Uuid\Uuid;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Services\FlashcardPollManager;
use Flashcard\Application\Services\FlashcardPollResolver;
use Flashcard\Application\Repository\IFlashcardPollRepository;

beforeEach(function () {
    $this->flashcard_poll_resolver = Mockery::mock(FlashcardPollResolver::class);
    $this->repository = Mockery::mock(IFlashcardPollRepository::class);
    $this->selector = Mockery::mock(IFlashcardSelector::class);
    $this->service = $this->app->make(FlashcardPollManager::class, [
        'resolver' => $this->flashcard_poll_resolver,
        'repository' => $this->repository,
        'selector' => $this->selector,
    ]);
    $this->repository->shouldReceive('save')->andReturn();
    $this->repository->shouldReceive('resetLeitnerLevelIfMaxLevelExceeded')->andReturn();
});
test('refresh when new poll add flashcards from selector to poll', function () {
    // GIVEN
    $user_id = new UserId(Uuid::uuid4()->toString());
    $flashcards = [
        Mockery::mock(Flashcard::class),
        Mockery::mock(Flashcard::class),
    ];

    $i = 1;
    foreach ($flashcards as $flashcard) {
        $flashcard->shouldReceive('getId')->andReturn(new FlashcardId($i));
        ++$i;
    }

    $poll = new FlashcardPoll($user_id, 0);
    $this->flashcard_poll_resolver->shouldReceive('resolve')->andReturn($poll);
    $this->selector->shouldReceive('selectToPoll')->andReturn($flashcards);

    // WHEN
    $poll = $this->service->refresh($user_id);

    // THEN
    expect(count($poll->getFlashcardIdsToAdd()))->toBe(2);
    expect($poll->getFlashcardIdsToAdd()[0]->getValue())->toBe($flashcards[0]->getId()->getValue());
    expect($poll->getFlashcardIdsToAdd()[1]->getValue())->toBe($flashcards[1]->getId()->getValue());
});
test('refresh when are flashcards to replace replace flashcards correctly', function () {
    // GIVEN
    $user_id = new UserId(Uuid::uuid4()->toString());
    $to_purge = new FlashcardIdCollection([
        new FlashcardId(9),
        new FlashcardId(10),
    ]);

    $to_add = [
        Mockery::mock(Flashcard::class),
        Mockery::mock(Flashcard::class),
    ];

    $i = 1;
    foreach ($to_add as $flashcard) {
        $flashcard->shouldReceive('getId')->andReturn(new FlashcardId($i));
        ++$i;
    }

    $poll = new FlashcardPoll($user_id, 2, $to_purge, new FlashcardIdCollection(), 2);
    $this->flashcard_poll_resolver->shouldReceive('resolve')->andReturn($poll);
    $this->selector->shouldReceive('selectToPoll')->andReturn($to_add);

    // WHEN
    $poll = $this->service->refresh($user_id);

    // THEN
    expect(count($poll->getFlashcardIdsToAdd()))->toBe(2);
    expect($poll->getFlashcardIdsToAdd()[0]->getValue())->toBe($to_add[0]->getId()->getValue());
    expect($poll->getFlashcardIdsToAdd()[1]->getValue())->toBe($to_add[1]->getId()->getValue());
    expect($poll->getFlashcardIdsToPurge()[0]->getValue())->toBe($to_purge[0]->getValue());
    expect($poll->getFlashcardIdsToPurge()[1]->getValue())->toBe($to_purge[1]->getValue());
});
test('refresh when no flashcards to replace should not replace flashcards', function () {
    // GIVEN
    $user_id = new UserId(Uuid::uuid4()->toString());
    $to_purge = new FlashcardIdCollection([
        new FlashcardId(9),
        new FlashcardId(10),
    ]);

    $to_add = [
        Mockery::mock(Flashcard::class),
        Mockery::mock(Flashcard::class),
    ];

    $i = 1;
    foreach ($to_add as $flashcard) {
        $flashcard->shouldReceive('getId')->andReturn(new FlashcardId($i));
        ++$i;
    }

    $poll = new FlashcardPoll($user_id, 2, $to_purge, new FlashcardIdCollection(), 4);
    $this->flashcard_poll_resolver->shouldReceive('resolve')->andReturn($poll);
    $this->selector->shouldReceive('selectToPoll')->andReturn($to_add);

    // WHEN
    $poll = $this->service->refresh($user_id);

    // THEN
    expect(count($poll->getFlashcardIdsToAdd()))->toBe(2);
    expect($poll->getFlashcardIdsToAdd()[0]->getValue())->toBe($to_add[0]->getId()->getValue());
    expect($poll->getFlashcardIdsToAdd()[1]->getValue())->toBe($to_add[1]->getId()->getValue());
    expect($poll->getFlashcardIdsToPurge())->toHaveCount(0);
});
