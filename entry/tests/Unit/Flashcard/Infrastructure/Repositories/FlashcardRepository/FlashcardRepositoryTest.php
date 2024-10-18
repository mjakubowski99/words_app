<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardRepository;

use Tests\TestCase;
use App\Models\User;
use App\Models\Flashcard;
use App\Models\LearningSession;
use App\Models\FlashcardCategory;
use Shared\Utils\ValueObjects\Language;
use App\Models\LearningSessionFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\FlashcardRepository;

class FlashcardRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
    }

    public function test__getRandomFlashcards_returnUserFlashcards(): void
    {
        // GIVEN
        $owner = User::factory()->create();
        $other_flashcard = Flashcard::factory()->create();
        $flashcard = Flashcard::factory()->create(['user_id' => $owner->id]);

        // WHEN
        $flashcards = $this->repository->getRandomFlashcards($owner->toOwner(), 5, []);

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(\Flashcard\Domain\Models\Flashcard::class, $flashcards[0]);
        $this->assertSame($flashcard->getId()->getValue(), $flashcards[0]->getId()->getValue());
    }

    public function test__getRandomFlashcardsByCategory_returnOnlyFlashcardsForGivenCategory(): void
    {
        // GIVEN
        $category = FlashcardCategory::factory()->create();
        $other_flashcard = Flashcard::factory()->create();
        $flashcard = Flashcard::factory()->create(['flashcard_category_id' => $category->id]);

        // WHEN
        $flashcards = $this->repository->getRandomFlashcardsByCategory($category->getId(), 5, []);

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(\Flashcard\Domain\Models\Flashcard::class, $flashcards[0]);
        $this->assertSame($flashcard->getId()->getValue(), $flashcards[0]->getId()->getValue());
    }

    public function test__getByCategory_returnOnlyFlashcardsForGivenCategory(): void
    {
        // GIVEN
        $category = FlashcardCategory::factory()->create();
        $other_flashcard = Flashcard::factory()->create();
        $flashcard = Flashcard::factory()->create(['flashcard_category_id' => $category->id]);

        // WHEN
        $flashcards = $this->repository->getByCategory($category->getId());

        // THEN
        $this->assertCount(1, $flashcards);
        $this->assertInstanceOf(\Flashcard\Domain\Models\Flashcard::class, $flashcards[0]);
        $this->assertSame($flashcard->getId()->getValue(), $flashcards[0]->getId()->getValue());
    }

    public function test__replaceCategory_replaceCategoryForCorrectFlashcards(): void
    {
        // GIVEN
        $actual_category = FlashcardCategory::factory()->create();
        $flashcards = Flashcard::factory(2)->create([
            'flashcard_category_id' => $actual_category->id,
        ]);
        $other_flashcard = Flashcard::factory()->create();
        $new_category = FlashcardCategory::factory()->create();

        // WHEN
        $this->repository->replaceCategory($actual_category->getId(), $new_category->getId());

        // THEN
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_category_id' => $new_category->id,
            ]);
        }
        $this->assertDatabaseHas('flashcards', [
            'id' => $other_flashcard->id,
            'flashcard_category_id' => $other_flashcard->flashcard_category_id,
        ]);
    }

    public function test__getLatestSessionFlashcardIds_ShouldReturnLatestSessionFlashcardIds(): void
    {
        // GIVEN
        $session = LearningSession::factory()->create();
        $session_flashcards = [
            LearningSessionFlashcard::factory()->create([
                'learning_session_id' => $session->id,
                'created_at' => now()->subMinute(),
            ]),
            LearningSessionFlashcard::factory()->create([
                'learning_session_id' => $session->id,
                'created_at' => now()->subSecond(),
            ]),
            LearningSessionFlashcard::factory()->create([
                'learning_session_id' => $session->id,
                'created_at' => now()->subSeconds(2),
            ]),
        ];

        // WHEN
        $results = $this->repository->getLatestSessionFlashcardIds($session->getId(), 1);

        // THEN
        $this->assertCount(1, $results);
        $this->assertInstanceOf(FlashcardId::class, $results[0]);
        $this->assertSame($session_flashcards[1]->flashcard->id, $results[0]->getValue());
    }

    public function test__createMany_ShouldCreateFlashcards(): void
    {
        // GIVEN
        $users = User::factory(2)->create();
        $categories = FlashcardCategory::factory(2)->create();

        $flashcards = [
            new \Flashcard\Domain\Models\Flashcard(
                new FlashcardId(0),
                'word',
                Language::from('en'),
                'trans',
                Language::from('pl'),
                'context',
                'context_translation',
                $users[0]->toOwner(),
                $categories[0]->toDomainModel()
            ),
            new \Flashcard\Domain\Models\Flashcard(
                new FlashcardId(0),
                'word 1',
                Language::from('pl'),
                'trans 1',
                Language::from('en'),
                'context 1',
                'context_translation 1',
                $users[1]->toOwner(),
                $categories[1]->toDomainModel()
            ),
        ];

        // WHEN
        $this->repository->createMany($flashcards);

        // THEN
        $this->assertDatabaseHas('flashcards', [
            'word' => 'word',
            'word_lang' => 'en',
            'translation' => 'trans',
            'translation_lang' => 'pl',
            'context' => 'context',
            'context_translation' => 'context_translation',
            'flashcard_category_id' => $categories[0]->id,
            'user_id' => $users[0]->id,
        ]);
        $this->assertDatabaseHas('flashcards', [
            'word' => 'word 1',
            'word_lang' => 'pl',
            'translation' => 'trans 1',
            'translation_lang' => 'en',
            'context' => 'context 1',
            'context_translation' => 'context_translation 1',
            'flashcard_category_id' => $categories[1]->id,
            'user_id' => $users[1]->id,
        ]);
    }
}
