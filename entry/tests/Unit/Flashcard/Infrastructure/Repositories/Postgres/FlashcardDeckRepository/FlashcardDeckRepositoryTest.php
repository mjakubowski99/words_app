<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckRepository;

use Tests\TestCase;
use App\Models\User;
use App\Models\FlashcardDeck;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckRepository;

class FlashcardDeckRepositoryTest extends TestCase
{
    private FlashcardDeckRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardDeckRepository::class);
    }

    public function test__findById_WhenNormalDeck_success(): void
    {
        // GIVEN
        $other_deck = FlashcardDeck::factory()->create();
        $deck = FlashcardDeck::factory()->create();

        // WHEN
        $result = $this->repository->findById($deck->getId());

        // THEN
        $this->assertInstanceOf(Deck::class, $result);
        $this->assertSame($deck->getId()->getValue(), $result->getId()->getValue());
        $this->assertSame($deck->name, $result->getName());
        $this->assertSame($deck->user->getId()->getValue(), $result->getOwner()->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::USER, $result->getOwner()->getOwnerType());
    }

    public function test__create_ShouldCreateDeck(): void
    {
        // GIVEN
        $deck = \Mockery::mock(Deck::class);
        $user = User::factory()->create();
        $deck->allows([
            'getName' => 'Cat name',
            'hasOwner' => true,
            'getOwner' => $user->toOwner(),
        ]);

        // WHEN
        $this->repository->create($deck);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'name' => 'Cat name',
            'user_id' => $user->id,
        ]);
    }

    public function test__getByOwner_ReturnOnlyUserDecks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $other_deck = FlashcardDeck::factory()->create();
        $user_deck = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), 1, 15);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Deck::class, $results[0]);
        $this->assertSame($user_deck->id, $results[0]->getId()->getValue());
        $this->assertSame($user_deck->name, $results[0]->getName());
        $this->assertSame($user_deck->user_id, $results[0]->getOwner()->getId()->getValue());
        $this->assertSame(FlashcardOwnerType::USER, $results[0]->getOwner()->getOwnerType());
    }

    public function test__getByOwner_paginationWorks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $user_decks = FlashcardDeck::factory(2)->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        $results = $this->repository->getByOwner($user->toOwner(), 2, 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Deck::class, $results[0]);
        $this->assertSame($user_decks[1]->id, $results[0]->getId()->getValue());
    }

    public function test__searchByName_shouldReturnUserDeck(): void
    {
        // GIVEN
        $user = User::factory()->create();
        FlashcardDeck::factory()->create(['name' => 'deck']);
        $expected_deck = FlashcardDeck::factory()->create(['name' => 'deck', 'user_id' => $user->id]);
        $owner = new Owner(new OwnerId($user->id), FlashcardOwnerType::USER);

        // WHEN
        $deck = $this->repository->searchByName($owner, 'deck');

        // THEN
        $this->assertSame($expected_deck->id, $deck->getId()->getValue());
        $this->assertSame($expected_deck->name, $deck->getName());
        $this->assertSame($user->id, $deck->getOwner()->getId()->getValue());
    }

    public function test__searchByName_shouldCorrectlySearchByNAME(): void
    {
        // GIVEN
        $user = User::factory()->create();
        FlashcardDeck::factory()->create(['name' => 'deck 1', 'user_id' => $user->id]);
        $expected_deck = FlashcardDeck::factory()->create(['name' => 'deck', 'user_id' => $user->id]);
        $owner = new Owner(new OwnerId($user->id), FlashcardOwnerType::USER);

        // WHEN
        $deck = $this->repository->searchByName($owner, 'deck');

        // THEN
        $this->assertSame($expected_deck->id, $deck->getId()->getValue());
        $this->assertSame($expected_deck->name, $deck->getName());
    }
}
