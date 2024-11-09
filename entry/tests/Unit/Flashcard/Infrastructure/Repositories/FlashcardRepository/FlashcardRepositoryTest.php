<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\FlashcardRepository;

use App\Models\User;
use App\Models\FlashcardCategory;
use Tests\Base\FlashcardTestCase;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\FlashcardRepository;

class FlashcardRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardRepository::class);
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
