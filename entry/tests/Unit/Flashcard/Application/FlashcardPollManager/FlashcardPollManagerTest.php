<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Application\FlashcardPollManager;

use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Mockery\MockInterface;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Types\FlashcardIdCollection;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Services\FlashcardPollManager;
use Flashcard\Application\Services\FlashcardPollResolver;
use Flashcard\Application\Repository\IFlashcardPollRepository;

class FlashcardPollManagerTest extends TestCase
{
    private FlashcardPollManager $service;

    private FlashcardPollResolver $flashcard_poll_resolver;
    private IFlashcardPollRepository|MockInterface $repository;
    private IFlashcardSelector|MockInterface $selector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flashcard_poll_resolver = \Mockery::mock(FlashcardPollResolver::class);
        $this->repository = \Mockery::mock(IFlashcardPollRepository::class);
        $this->selector = \Mockery::mock(IFlashcardSelector::class);
        $this->service = $this->app->make(FlashcardPollManager::class, [
            'resolver' => $this->flashcard_poll_resolver,
            'repository' => $this->repository,
            'selector' => $this->selector,
        ]);
        $this->repository->shouldReceive('save')->andReturn();
        $this->repository->shouldReceive('resetLeitnerLevelIfMaxLevelExceeded')->andReturn();
    }

    /**
     * @test
     */
    public function refresh_WhenNewPoll_AddFlashcardsFromSelectorToPoll(): void
    {
        // GIVEN
        $user_id = new UserId(Uuid::uuid4()->toString());
        $flashcards = [
            \Mockery::mock(Flashcard::class),
            \Mockery::mock(Flashcard::class),
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
        $this->assertSame(2, count($poll->getFlashcardIdsToAdd()));
        $this->assertSame($flashcards[0]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[0]->getValue());
        $this->assertSame($flashcards[1]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[1]->getValue());
    }

    /**
     * @test
     */
    public function refresh_WhenAreFlashcardsToReplace_ReplaceFlashcardsCorrectly(): void
    {
        // GIVEN
        $user_id = new UserId(Uuid::uuid4()->toString());
        $to_purge = new FlashcardIdCollection([
            new FlashcardId(9),
            new FlashcardId(10),
        ]);

        $to_add = [
            \Mockery::mock(Flashcard::class),
            \Mockery::mock(Flashcard::class),
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
        $this->assertSame(2, count($poll->getFlashcardIdsToAdd()));
        $this->assertSame($to_add[0]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[0]->getValue());
        $this->assertSame($to_add[1]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[1]->getValue());
        $this->assertSame($to_purge[0]->getValue(), $poll->getFlashcardIdsToPurge()[0]->getValue());
        $this->assertSame($to_purge[1]->getValue(), $poll->getFlashcardIdsToPurge()[1]->getValue());
    }

    /**
     * @test
     */
    public function refresh_WhenNoFlashcardsToReplace_ShouldNotReplaceFlashcards(): void
    {
        // GIVEN
        $user_id = new UserId(Uuid::uuid4()->toString());
        $to_purge = new FlashcardIdCollection([
            new FlashcardId(9),
            new FlashcardId(10),
        ]);

        $to_add = [
            \Mockery::mock(Flashcard::class),
            \Mockery::mock(Flashcard::class),
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
        $this->assertSame(2, count($poll->getFlashcardIdsToAdd()));
        $this->assertSame($to_add[0]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[0]->getValue());
        $this->assertSame($to_add[1]->getId()->getValue(), $poll->getFlashcardIdsToAdd()[1]->getValue());
        $this->assertCount(0, $poll->getFlashcardIdsToPurge());
    }
}
