<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\SmTwoFlashcardRepository;

use App\Models\User;
use App\Models\Admin;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\SmTwoFlashcard;
use App\Models\FlashcardPollItem;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Application\Repository\FlashcardSortCriteria;
use Flashcard\Infrastructure\Repositories\Postgres\SmTwoFlashcardRepository;

class SmTwoFlashcardRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private SmTwoFlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(SmTwoFlashcardRepository::class);
    }

    /**
     * @test
     */
    public function findMany_ShouldReturnCorrectSmTwoFlashcards(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $expected_flashcards = [
            SmTwoFlashcard::factory()->create(['user_id' => $user->id, 'min_rating' => 3]),
            SmTwoFlashcard::factory()->create(['user_id' => $user->id]),
        ];
        $other_flashcards = SmTwoFlashcard::factory()->create(['user_id' => $user->id]);
        $flashcard_ids = array_map(fn (SmTwoFlashcard $flashcard) => $flashcard->getFlashcardId(), $expected_flashcards);

        // WHEN
        $results = $this->repository->findMany($user->getId(), $flashcard_ids);

        // THEN
        $this->assertSame(2, count($results));
        $this->assertSame(3, $results->all()[0]->getMinRating());
    }

    /**
     * @test
     */
    public function resetRepetitionsInSession_success(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $other_flashcard = SmTwoFlashcard::factory()->create([
            'repetitions_in_session' => 4,
        ]);
        $flashcard = SmTwoFlashcard::factory()->create([
            'user_id' => $user->getId()->getValue(),
            'repetitions_in_session' => 3,
        ]);

        // WHEN
        $this->repository->resetRepetitionsInSession($user->getId());

        // THEN
        $this->assertDatabaseHas('sm_two_flashcards', [
            'flashcard_id' => $flashcard->flashcard_id,
            'repetitions_in_session' => 0,
        ]);
        $this->assertDatabaseHas('sm_two_flashcards', [
            'flashcard_id' => $other_flashcard->flashcard_id,
            'repetitions_in_session' => 4,
        ]);
    }

    /**
     * @test
     */
    public function saveMany_ShouldSave(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $flashcard = SmTwoFlashcard::factory()->create([
            'user_id' => $user->getId()->getValue(),
            'repetition_interval' => 1,
            'repetition_count' => 2,
            'repetition_ratio' => 3,
            'min_rating' => 2,
            'repetitions_in_session' => 3,
        ]);
        $domain_model = new SmTwoFlashcards([
            $flashcard->toDomainModel(),
        ]);

        // WHEN
        $this->repository->saveMany($domain_model);

        // THEN
        $this->assertDatabaseHas('sm_two_flashcards', [
            'flashcard_id' => $flashcard->flashcard_id,
            'user_id' => $flashcard->user_id,
            'repetition_ratio' => $flashcard->repetition_ratio,
            'repetition_interval' => $flashcard->repetition_interval,
            'repetition_count' => $flashcard->repetition_count,
            'min_rating' => $flashcard->min_rating,
            'repetitions_in_session' => $flashcard->repetitions_in_session,
        ]);
    }

    /**
     * @test
     */
    public function getNextFlashcardsByDeck_ShouldReturnFlashcards(): void
    {
        // GIVEN
        $user = User::factory()->create();
        $deck = FlashcardDeck::factory()->create();

        $flashcard = Flashcard::factory()->create(['flashcard_deck_id' => $deck->id]);
        $sm_two_flashcards = [
            SmTwoFlashcard::factory()->create([
                'flashcard_id' => Flashcard::factory()->create([
                    'flashcard_deck_id' => $deck->id,
                    'user_id' => null,
                    'admin_id' => Admin::factory()->create()->id,
                ]),
                'user_id' => $user->id,
                'repetition_interval' => 2,
                'updated_at' => now()->subDays(3),
            ]),
            SmTwoFlashcard::factory()->create([
                'flashcard_id' => Flashcard::factory()->create([
                    'flashcard_deck_id' => $deck->id,
                    'user_id' => $user->id,
                    'admin_id' => null,
                ]),
                'user_id' => $user->id,
                'repetition_interval' => 3,
                'updated_at' => now()->subDays(3),
            ]),
        ];

        $flashcard_poll = FlashcardPollItem::factory()->create([
            'user_id' => $user->id,
            'flashcard_id' => $sm_two_flashcards[1]->flashcard_id,
        ]);

        // WHEN
        $results = $this->repository->getNextFlashcardsByDeck($user->getId(), $deck->getId(), 5, [], [
            FlashcardSortCriteria::HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
            FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER,
            FlashcardSortCriteria::NOT_RATED_FLASHCARDS_FIRST,
            FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER,
            FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST,
            FlashcardSortCriteria::LOWEST_REPETITION_INTERVAL_FIRST,
        ], 2, true);

        // THEN
        $this->assertCount(1, $results);
        $this->assertSame($flashcard_poll->flashcard_id, $results[0]->getId()->getValue());
    }
}
