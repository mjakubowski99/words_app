<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\StoryRepository;

use Tests\TestCase;
use App\Models\StoryFlashcard;
use Flashcard\Domain\Models\Story;
use Shared\Utils\ValueObjects\StoryId;
use Flashcard\Domain\Models\StoryCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\StoryRepository;

class StoryRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use StoryRepositoryTrait;

    private StoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(StoryRepository::class);
    }

    public function test__findRandomStoryIdByFlashcardId_WhenStoryExists_ReturnsStory(): void
    {
        // GIVEN
        $user = $this->createUser();
        $story = $this->createStory();
        $flashcard = $this->createFlashcard([
            'front_word' => 'test',
            'user_id' => $user->id,
        ]);
        $this->createStoryWithFlashcard($story, $flashcard, 'Test sentence');

        // WHEN
        $result = $this->repository->findRandomStoryIdByFlashcard($flashcard->getId());

        // THEN
        $this->assertInstanceOf(StoryId::class, $result);
        $this->assertSame($story->id, $result->getValue());
    }

    public function test__find_WhenStoryExists_ReturnsStory(): void
    {
        // GIVEN
        $user = $this->createUser();
        $story = $this->createStory();
        $flashcard = $this->createFlashcard([
            'front_word' => 'test',
            'user_id' => $user->id,
        ]);
        $this->createStoryWithFlashcard($story, $flashcard, 'Test sentence');

        // WHEN
        $result = $this->repository->find(new StoryId($story->id), $user->getId());

        // THEN
        $this->assertInstanceOf(Story::class, $result);
        $this->assertSame($story->id, $result->getId()->getValue());
        $this->assertSame($flashcard->front_word, $result->getStoryFlashcards()[0]->getFlashcard()->getFrontWord());
        $this->assertSame($flashcard->back_word, $result->getStoryFlashcards()[0]->getFlashcard()->getBackWord());
    }

    public function test__saveMany_CreatesStoriesWithFlashcards(): void
    {
        // GIVEN
        $user = $this->createUser();
        $deck = $this->createFlashcardDeck([
            'user_id' => $user->id,
        ]);
        $stories = [];

        $story_flashcards = [
            $this->createNewStoryFlashcard($user, $deck, 'word', 'translation', 'Sentence'),
            $this->createNewStoryFlashcard($user, $deck, 'word 2', 'translation 2', 'Sentence 2'),
        ];
        $stories[] = new Story(
            StoryId::noId(),
            $story_flashcards
        );

        $story_flashcards = [
            $this->createNewStoryFlashcard($user, $deck, 'wx', 't', 'Se'),
            $this->createNewStoryFlashcard($user, $deck, 'w2', 't2', 'Sen'),
        ];
        $stories[] = new Story(
            StoryId::noId(),
            $story_flashcards
        );

        // WHEN
        $this->repository->saveMany(new StoryCollection($stories));

        // THEN
        $stories = \App\Models\Story::all();
        $this->assertCount(2, $stories);

        $story_flashcards = StoryFlashcard::query()->where('story_id', $stories[0]->id)->get();

        $this->assertSame('word', $story_flashcards[0]->flashcard->front_word);
        $this->assertSame('word 2', $story_flashcards[1]->flashcard->front_word);

        $story_flashcards = StoryFlashcard::query()->where('story_id', $stories[1]->id)->get();

        $this->assertSame('wx', $story_flashcards[0]->flashcard->front_word);
        $this->assertSame('w2', $story_flashcards[1]->flashcard->front_word);
    }

    public function test__saveMany_WhenSentenceOverride_SavesWithSentenceOverride(): void
    {
        // GIVEN
        $user = $this->createUser();
        $deck = $this->createFlashcardDeck([
            'user_id' => $user->id,
        ]);

        $story_flashcards = [
            $this->createNewStoryFlashcard($user, $deck, 'word', 'translation', 'Sentence', 'Override'),
        ];
        $story = new Story(
            StoryId::noId(),
            $story_flashcards
        );

        // WHEN
        $this->repository->saveMany(new StoryCollection([$story]));

        // THEN
        $stories = \App\Models\Story::all();
        $this->assertCount(1, $stories);

        $story_flashcards = StoryFlashcard::query()->where('story_id', $stories[0]->id)->get();

        $this->assertSame('Override', $story_flashcards[0]->sentence_override);
    }

    public function test__bulkDelete_deleteStories(): void
    {
        // GIVEN
        $to_delete = [
            StoryFlashcard::factory()->create(),
            StoryFlashcard::factory()->create(),
        ];
        $to_keep = StoryFlashcard::factory()->create();

        // WHEN
        $this->repository->bulkDelete([new StoryId($to_delete[0]->story_id), new StoryId($to_delete[1]->story_id)]);

        // THEN
        $this->assertDatabaseMissing('story_flashcards', ['story_id' => $to_delete[0]->story_id]);
        $this->assertDatabaseMissing('story_flashcards', ['story_id' => $to_delete[1]->story_id]);
        $this->assertDatabaseHas('stories', ['id' => $to_keep->story_id]);
        $this->assertDatabaseHas('flashcards', ['id' => $to_delete[0]->flashcard_id]);
        $this->assertDatabaseHas('flashcards', ['id' => $to_delete[1]->flashcard_id]);
    }
}
