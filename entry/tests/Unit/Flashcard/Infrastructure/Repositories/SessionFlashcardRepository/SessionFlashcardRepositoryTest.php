<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\SessionFlashcardRepository;

use App\Models\LearningSession;
use Tests\Base\FlashcardTestCase;
use Shared\Utils\ValueObjects\UserId;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SessionFlashcards;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\SessionFlashcardRepository;

class SessionFlashcardRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private SessionFlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(SessionFlashcardRepository::class);
    }

    /**
     * @test
     */
    public function findMany_ShouldReturnOnlyUserSessionFlashcards(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $expected_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
        ]);
        $other_flashcard = LearningSessionFlashcard::factory()->create();
        $ids = [
            $this->createSessionFlashcardId($expected_flashcard),
            $this->createSessionFlashcardId($other_flashcard),
        ];

        // WHEN
        $result = $this->repository->findMany($session->getId(), $ids);

        // THEN
        $this->assertSame(1, count($result->all()));
        $this->assertSame($expected_flashcard->id, $result->all()[0]->getId()->getValue());
    }

    /**
     * @test
     */
    public function findMany_ShouldReturnCorrectSessionFlashcards()
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $user_id = new UserId($session->user_id);
        $expected_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
        ]);
        $other_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
        ]);
        $ids = [
            $this->createSessionFlashcardId($expected_flashcard),
        ];

        // WHEN
        $result = $this->repository->findMany($session->getId(), $ids);

        // THEN
        $this->assertSame(1, count($result->all()));
        $this->assertSame($expected_flashcard->id, $result->all()[0]->getId()->getValue());
    }

    /**
     * @test
     */
    public function saveRating_ShouldSaveRatings(): void
    {
        // GIVEN
        $flashcard_id = new FlashcardId(1);
        $session = LearningSession::factory()->create();

        $learning_session_flashcards = LearningSessionFlashcard::factory(2)->create();

        $session_flashcards_to_rate = new SessionFlashcards(
            $session->toDomainModel(),
            [
                $learning_session_flashcards[0]->toDomainModel(),
                $learning_session_flashcards[1]->toDomainModel(),
            ]
        );

        // WHEN
        $this->repository->saveRating($session_flashcards_to_rate);

        // THEN
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcards_to_rate->all()[0]->getId()->getValue(),
            'rating' => $session_flashcards_to_rate->all()[0]->getRating()->value,
        ]);
        $this->assertDatabaseHas('learning_session_flashcards', [
            'id' => $session_flashcards_to_rate->all()[1]->getId()->getValue(),
            'rating' => $session_flashcards_to_rate->all()[1]->getRating()->value,
        ]);
    }
}
