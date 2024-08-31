<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\SessionFlashcardRepository;

use App\Models\LearningSession;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Infrastructure\DatabaseRepositories\SessionFlashcardRepository;
use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Uuid;
use Tests\Base\FlashcardTestCase;

class SessionFlashcardRepositoryTest extends FlashcardTestCase
{
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
        $user_id = new UserId($session->user_id);
        $expected_flashcard = LearningSessionFlashcard::factory()->create([
            'learning_session_id' => $session->id,
        ]);
        $other_flashcard = LearningSessionFlashcard::factory()->create();
        $ids = [
            $this->createSessionFlashcardId($expected_flashcard),
            $this->createSessionFlashcardId($other_flashcard),
        ];

        // WHEN
        $result = $this->repository->findMany($user_id, $ids);

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
        $result = $this->repository->findMany($user_id, $ids);

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
        $flashcard_id = new FlashcardId('1');
        $session_flashcards_to_rate = new SessionFlashcards([
            new SessionFlashcard(new SessionFlashcardId(LearningSessionFlashcard::factory()->create()->id), $flashcard_id, Rating::VERY_GOOD),
            new SessionFlashcard(new SessionFlashcardId(LearningSessionFlashcard::factory()->create()->id), $flashcard_id, Rating::GOOD)
        ], new UserId(Uuid::make()->getValue()));

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
