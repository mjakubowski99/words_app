<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Illuminate\Support\Arr;
use Flashcard\Domain\Models\Story;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\StoryId;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class StoryMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private FlashcardMapper $mapper
    ) {}

    public function findRandomStoryIdByFlashcardId(FlashcardId $id): ?StoryId
    {
        $story = DB::table('story_flashcards')
            ->where('flashcard_id', $id->getValue())
            ->inRandomOrder()
            ->select('story_id')
            ->first();

        return $story ? new StoryId($story->story_id) : null;
    }

    public function find(StoryId $id, UserId $user_id): ?Story
    {
        $rows = DB::table('story_flashcards')
            ->where('story_id', $id->getValue())
            ->select(
                'story_flashcards.flashcard_id',
                'story_flashcards.story_id',
                'story_flashcards.sentence_override',
            )
            ->get();

        $flashcards = $this->mapper->findManyForUser(
            $rows->pluck('flashcard_id')->toArray(),
            $user_id
        );

        $story_flashcards = [];
        foreach ($rows as $row) {
            $story_flashcards[] = new StoryFlashcard(
                new StoryId($row->story_id),
                $row->story_id,
                $row->sentence_override,
                Arr::first($flashcards, fn (Flashcard $flashcard) => $flashcard->getId()->getValue() === $row->flashcard_id)
            );
        }

        return new Story(
            $id,
            $story_flashcards
        );
    }

    /** @param Story[] $stories */
    public function saveMany(StoryCollection $stories): void
    {
        $now = now();

        $insert_data = [];
        $i = 0;
        foreach ($stories->get() as $story) {
            $insert_data[] = [
                'created_at' => (clone $now)->addSeconds($i),
                'updated_at' => (clone $now)->addSeconds($i),
            ];
            ++$i;
        }

        $story_ids = DB::table('stories')
            ->insertReturning($insert_data)
            ->sortBy('created_at')
            ->values();

        $i = 0;
        foreach ($stories->get() as $story) {
            foreach ($story->getStoryFlashcards() as $story_flashcard) {
                $story_flashcard->setStoryId(new StoryId($story_ids[$i]->id));
            }
            ++$i;
        }

        $stories = $this->mapper->createManyFromStoryFlashcards($stories);

        $insert_data = [];
        foreach ($stories->getAllStoryFlashcards() as $story_flashcard) {
            $insert_data[] = [
                'story_id' => $story_flashcard->getStoryId()->getValue(),
                'flashcard_id' => $story_flashcard->getFlashcard()->getId()->getValue(),
                'sentence_override' => $story_flashcard->getSentenceOverride(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('story_flashcards')->insert($insert_data);
    }

    public function bulkDelete(array $story_ids): void
    {
        DB::table('stories')
            ->whereIn('id', array_map(fn (StoryId $id) => $id->getValue(), $story_ids))
            ->delete();
    }
}
