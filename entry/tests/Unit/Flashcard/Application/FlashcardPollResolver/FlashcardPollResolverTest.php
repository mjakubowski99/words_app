<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Application\FlashcardPollResolver;

use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Mockery\MockInterface;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Services\FlashcardPollResolver;
use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Domain\Exceptions\FlashcardPollOverLoadedException;

class FlashcardPollResolverTest extends TestCase
{
    use DatabaseTransactions;

    private FlashcardPollResolver $service;

    private IFlashcardPollRepository|MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = \Mockery::mock(IFlashcardPollRepository::class);
        $this->service = $this->app->make(FlashcardPollResolver::class, [
            'repository' => $this->repository,
        ]);
    }

    public function test__resolve_WhenPollNotOverloaded_shouldNotDeleteFlashcards(): void
    {
        // GIVEN
        $expected_user_id = new UserId(Uuid::uuid4()->toString());
        $poll = \Mockery::mock(FlashcardPoll::class);
        $this->repository->shouldReceive('findByUser')->andReturn($poll);

        $purge_expectation = $this->repository->shouldReceive('purgeLatestFlashcards')->andReturn();

        // WHEN
        $this->service->resolve($expected_user_id);

        // THEN
        $purge_expectation->never();
    }

    public function test__resolve_WhenPollOverloaded_shouldDeleteNotNeededFlashcards(): void
    {
        // GIVEN
        $max_poll_size = 4;
        $current_size = 6;
        $expected_limit = $current_size - $max_poll_size;
        $expected_user_id = new UserId(Uuid::uuid4()->toString());
        $poll = \Mockery::mock(FlashcardPoll::class);
        $callbacks = [
            fn () => throw new FlashcardPollOverLoadedException($max_poll_size, $current_size),
            fn () => $poll,
        ];
        $index = 0;
        $find_expectation = $this->repository->shouldReceive('findByUser')
            ->andReturnUsing(function () use (&$index, $callbacks) {
                $callback = $callbacks[$index] ?? end($callbacks);
                ++$index;

                return $callback();
            });
        $purge_expectation = $this->repository->shouldReceive('purgeLatestFlashcards')->withArgs(
            function (UserId $user_id, int $limit) use ($expected_user_id, $expected_limit) {
                $this->assertSame($expected_user_id->getValue(), $user_id->getValue());
                $this->assertSame($expected_limit, $limit);

                return true;
            }
        );

        // WHEN
        $this->service->resolve($expected_user_id);

        // THEN
        $find_expectation->twice();
        $purge_expectation->once();
    }
}
