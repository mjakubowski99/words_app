<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Exceptions\ModelNotFoundException;

class FlashcardMapper
{
    public function __construct(private readonly DB $db) {}

    public function getByCategory(CategoryId $id): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_category_id', $id->getValue())
            ->leftJoin('flashcard_categories', 'flashcard_categories.id', '=', 'flashcards.flashcard_category_id')
            ->select(
                'flashcards.*',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag as category_tag',
                'flashcard_categories.name as category_name',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.user_id', $owner->getId()->getValue())
            ->leftJoin('flashcard_categories', 'flashcard_categories.id', '=', 'flashcards.flashcard_category_id')
            ->whereNotIn('flashcards.id', $exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->select(
                'flashcards.*',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag as category_tag',
                'flashcard_categories.name as category_name',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_category_id', $id->getValue())
            ->leftJoin('flashcard_categories', 'flashcard_categories.id', '=', 'flashcards.flashcard_category_id')
            ->whereNotIn('flashcards.id', $exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->select(
                'flashcards.*',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag as category_tag',
                'flashcard_categories.name as category_name',
            )
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function createMany(array $flashcards): void
    {
        $insert_data = [];
        $now = now();

        /** @var Flashcard $flashcard */
        foreach ($flashcards as $flashcard) {
            $insert_data[] = [
                'user_id' => $flashcard->getOwner()->getId(),
                'flashcard_category_id' => $flashcard->getCategory()->getId(),
                'word' => $flashcard->getWord(),
                'word_lang' => $flashcard->getWordLang()->getValue(),
                'translation' => $flashcard->getTranslation(),
                'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                'context' => $flashcard->getContext(),
                'context_translation' => $flashcard->getContextTranslation(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db::table('flashcards')->insert($insert_data);
    }

    public function find(FlashcardId $id): Flashcard
    {
        $result = $this->db::table('flashcards')
            ->where('flashcards.id', $id)
            ->leftJoin('flashcard_categories', 'flashcard_categories.id', '=', 'flashcards.flashcard_category_id')
            ->select(
                'flashcards.*',
                'flashcard_categories.user_id as category_user_id',
                'flashcard_categories.tag as category_tag',
                'flashcard_categories.name as category_name',
            )
            ->first();

        if (!$result) {
            throw new ModelNotFoundException();
        }

        return $this->map($result);
    }

    public function delete(FlashcardId $id): void
    {
        $this->db::table('flashcards')
            ->where('id', $id)
            ->delete();
    }

    public function update(Flashcard $flashcard): void
    {
        $now = now();

        $this->db::table('flashcards')
            ->where('id', $flashcard->getId())
            ->update([
                'user_id' => $flashcard->getOwner()->getId(),
                'flashcard_category_id' => $flashcard->getCategory()->getId(),
                'word' => $flashcard->getWord(),
                'word_lang' => $flashcard->getWordLang()->getValue(),
                'translation' => $flashcard->getTranslation(),
                'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                'context' => $flashcard->getContext(),
                'context_translation' => $flashcard->getContextTranslation(),
                'updated_at' => $now,
            ]);
    }

    public function replaceCategory(CategoryId $actual_category_id, CategoryId $new_category_id): bool
    {
        $this->db::table('flashcards')
            ->where('flashcard_category_id', $actual_category_id)
            ->update([
                'flashcard_category_id' => $new_category_id,
                'updated_at' => now(),
            ]);

        return true;
    }

    public function replaceInSessions(CategoryId $actual_category_id, CategoryId $new_category_id): bool
    {
        $this->db::table('learning_sessions')
            ->where('flashcard_category_id', $actual_category_id)
            ->update([
                'flashcard_category_id' => $new_category_id,
                'updated_at' => now(),
            ]);

        return true;
    }

    public function map(object $data): Flashcard
    {
        $category = $data->flashcard_category_id ? (new Category(
            new Owner(new OwnerId($data->category_user_id), FlashcardOwnerType::USER),
            $data->category_tag,
            $data->category_name,
        ))->init(new CategoryId($data->flashcard_category_id)) : null;

        return new Flashcard(
            new FlashcardId($data->id),
            $data->word,
            Language::from($data->word_lang),
            $data->translation,
            Language::from($data->translation_lang),
            $data->context,
            $data->context_translation,
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            $category
        );
    }
}
