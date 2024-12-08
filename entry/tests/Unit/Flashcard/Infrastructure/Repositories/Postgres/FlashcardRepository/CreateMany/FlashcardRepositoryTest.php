<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository\CreateMany;

use App\Models\User;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

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
        $decks = FlashcardDeck::factory(2)->create();

        $flashcards = [
            new Flashcard(
                new FlashcardId(0),
                'word',
                Language::from('en'),
                'trans',
                Language::from('pl'),
                'context',
                'context_translation',
                $users[0]->toOwner(),
                $decks[0]->toDomainModel(),
                LanguageLevel::A1
            ),
            new Flashcard(
                new FlashcardId(0),
                'word 1',
                Language::from('pl'),
                'trans 1',
                Language::from('en'),
                'context 1',
                'context_translation 1',
                $users[1]->toOwner(),
                $decks[1]->toDomainModel(),
                LanguageLevel::A1
            ),
        ];

        // WHEN
        $this->repository->createMany($flashcards);

        // THEN
        $this->assertDatabaseHas('flashcards', [
            'front_word' => 'word',
            'front_lang' => 'en',
            'back_word' => 'trans',
            'back_lang' => 'pl',
            'front_context' => 'context',
            'back_context' => 'context_translation',
            'flashcard_deck_id' => $decks[0]->id,
            'user_id' => $users[0]->id,
        ]);
        $this->assertDatabaseHas('flashcards', [
            'front_word' => 'word 1',
            'front_lang' => 'pl',
            'back_word' => 'trans 1',
            'back_lang' => 'en',
            'front_context' => 'context 1',
            'back_context' => 'context_translation 1',
            'flashcard_deck_id' => $decks[1]->id,
            'user_id' => $users[1]->id,
        ]);
    }
}