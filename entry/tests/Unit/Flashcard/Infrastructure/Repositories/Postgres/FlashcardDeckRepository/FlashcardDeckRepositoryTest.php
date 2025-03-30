<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckRepository;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use Shared\Enum\LanguageLevel;
use App\Models\LearningSession;
use Flashcard\Domain\Models\Deck;
use Shared\Enum\FlashcardOwnerType;
use App\Models\FlashcardDeckActivity;
use Shared\Utils\ValueObjects\UserId;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckRepository;

class FlashcardDeckRepositoryTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test__findById_WhenAdminIsOwner_success(): void
    {
        // GIVEN
        $admin = Admin::factory()->create();
        $deck = FlashcardDeck::factory()->create([
            'user_id' => null,
            'admin_id' => $admin->id,
        ]);

        // WHEN
        $result = $this->repository->findById($deck->getId());

        // THEN
        $this->assertInstanceOf(Deck::class, $result);
        $this->assertTrue($result->getOwner()->equals($admin->toOwner()));
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
            'getDefaultLanguageLevel' => LanguageLevel::A1,
        ]);

        // WHEN
        $this->repository->create($deck);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'name' => 'Cat name',
            'user_id' => $user->id,
        ]);
    }

    public function test__create_WhenAdminIsOwner_ShouldCreateDeck(): void
    {
        // GIVEN
        $deck = \Mockery::mock(Deck::class);
        $admin = Admin::factory()->create();
        $deck->allows([
            'getName' => 'Cat name',
            'hasOwner' => true,
            'getOwner' => $admin->toOwner(),
            'getDefaultLanguageLevel' => LanguageLevel::A1,
        ]);

        // WHEN
        $this->repository->create($deck);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'name' => 'Cat name',
            'admin_id' => $admin->id,
        ]);
    }

    public function test__update_WhenUserIsOwner_ShouldUpdateDeck(): void
    {
        // GIVEN
        $deck_model = FlashcardDeck::factory()->create();

        $deck = \Mockery::mock(Deck::class);
        $user = User::factory()->create();
        $deck->allows([
            'getId' => $deck_model->getId(),
            'getName' => 'Cat',
            'hasOwner' => true,
            'getOwner' => $user->toOwner(),
            'getDefaultLanguageLevel' => LanguageLevel::A1,
        ]);

        // WHEN
        $this->repository->update($deck);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'id' => $deck_model->id,
            'name' => 'Cat',
            'admin_id' => null,
            'user_id' => $user->id,
        ]);
    }

    public function test__update_WhenAdminIsOwner_ShouldUpdateDeck(): void
    {
        // GIVEN
        $deck_model = FlashcardDeck::factory()->create();

        $deck = \Mockery::mock(Deck::class);
        $admin = Admin::factory()->create();
        $deck->allows([
            'getId' => $deck_model->getId(),
            'getName' => 'Cat name',
            'hasOwner' => true,
            'getOwner' => $admin->toOwner(),
            'getDefaultLanguageLevel' => LanguageLevel::A1,
        ]);

        // WHEN
        $this->repository->update($deck);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'id' => $deck_model->id,
            'admin_id' => $admin->id,
            'user_id' => null,
        ]);
    }

    public function test__updateLastViewedAt_NoEntryInDatabase(): void
    {
        // GIVEN
        Carbon::setTestNow('2023-10-12 12:00');
        $deck = FlashcardDeck::factory()->create();
        $user = User::factory()->create();

        // WHEN
        $this->repository->updateLastViewedAt($deck->getId(), $user->getId());

        // THEN
        $this->assertDatabaseHas(FlashcardDeckActivity::class, [
            'flashcard_deck_id' => $deck->getId(),
            'user_id' => $user->getId(),
            'last_viewed_at' => '2023-10-12 12:00',
        ]);
    }

    public function test__updateLastViewedAt_EntryInDatabase(): void
    {
        // GIVEN
        $activity = FlashcardDeckActivity::factory()->create();
        Carbon::setTestNow('2023-10-12 12:00');

        // WHEN
        $this->repository->updateLastViewedAt(new FlashcardDeckId($activity->flashcard_deck_id), new UserId($activity->user_id));

        // THEN
        $this->assertDatabaseHas(FlashcardDeckActivity::class, [
            'id' => $activity->id,
            'last_viewed_at' => '2023-10-12 12:00',
        ]);
    }

    public function test__getByUser_ReturnOnlyUserDecks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $other_deck = FlashcardDeck::factory()->create([
            'admin_id' => $admin->id,
            'user_id' => null,
        ]);
        $user_deck = FlashcardDeck::factory()->create([
            'user_id' => $user->id,
            'admin_id' => null,
        ]);

        // WHEN
        $results = $this->repository->getByUser($user->getId(), 1, 15);

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
        $results = $this->repository->getByUser($user->getId(), 2, 1);

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

        // WHEN
        $deck = $this->repository->searchByName($user->getId(), 'deck');

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

        // WHEN
        $deck = $this->repository->searchByName($user->getId(), 'deck');

        // THEN
        $this->assertSame($expected_deck->id, $deck->getId()->getValue());
        $this->assertSame($expected_deck->name, $deck->getName());
    }

    public function test__bulkDelete_ShouldDeleteOnlyDecksWithGivenIds(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $decks_to_delete = FlashcardDeck::factory(2)->byUser($user)->create();
        $decks_to_not_delete = FlashcardDeck::factory()->byUser($user)->create();
        $deck_ids = $decks_to_delete->map(fn (FlashcardDeck $deck) => $deck->getId())->toArray();

        // WHEN
        $this->repository->bulkDelete($user->getId(), $deck_ids);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'id' => $decks_to_not_delete->id,
        ]);
        foreach ($decks_to_delete as $deck) {
            $this->assertDatabaseMissing('flashcard_decks', [
                'id' => $deck->id,
            ]);
        }
    }

    public function test__bulkDelete_ShouldDeleteOnlyUserDecks(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $user_deck = FlashcardDeck::factory()->byUser($user)->create();
        $other_deck = FlashcardDeck::factory()->create();

        // WHEN
        $this->repository->bulkDelete($user->getId(), [$user_deck->getId(), $other_deck->getId()]);

        // THEN
        $this->assertDatabaseHas('flashcard_decks', [
            'id' => $other_deck->id,
        ]);
        $this->assertDatabaseMissing('flashcard_decks', [
            'id' => $user_deck->id,
        ]);
    }

    public function test__bulkDelete_ShouldDeleteDeckWithAllData(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $user_deck = FlashcardDeck::factory()->byUser($user)->create();
        $flashcard = Flashcard::factory()->create(['flashcard_deck_id' => $user_deck->id]);
        $sm_two_flashcard = SmTwoFlashcard::factory()->create([
            'flashcard_id' => $flashcard->id,
        ]);
        $learning_session = LearningSession::factory()->create(['flashcard_deck_id' => $user_deck->id]);
        $learning_session_flashcard_from_session = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $learning_session->id,
        ]);
        $learning_session_flashcard = LearningSessionFlashcard::factory()->create([
            'flashcard_id' => $flashcard->id,
        ]);

        // WHEN
        $this->repository->bulkDelete($user->getId(), [$user_deck->getId()]);

        // THEN
        $this->assertDatabaseMissing('flashcard_decks', [
            'id' => $user_deck->id,
        ]);
        $this->assertDatabaseMissing('sm_two_flashcards', [
            'flashcard_id' => $flashcard->id,
        ]);
        $this->assertDatabaseMissing('flashcards', [
            'id' => $flashcard->id,
        ]);
        $this->assertDatabaseMissing('learning_sessions', [
            'id' => $learning_session->id,
        ]);
        $this->assertDatabaseMissing('learning_session_flashcards', [
            'id' => $learning_session_flashcard->id,
        ]);
        $this->assertDatabaseMissing('learning_session_flashcards', [
            'id' => $learning_session_flashcard_from_session->id,
        ]);
    }
}
