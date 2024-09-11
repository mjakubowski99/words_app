<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\OwnerId;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\CategoryId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\FlashcardId;

class FlashcardMapper
{
    public function __construct(private readonly DB $db) {}

    public function getByCategory(CategoryId $id): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_category_id', $id->getValue())
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.user_id', $owner->getId()->getValue())
            ->whereNotIn('flashcards.id', $exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('flashcards')
            ->where('flashcards.flashcard_category_id', $id->getValue())
            ->whereNotIn('flashcards.id', $exclude_flashcard_ids)
            ->take($limit)
            ->inRandomOrder()
            ->get()
            ->map(function (object $data) {
                return $this->map($data);
            })->toArray();
    }

    public function createMany(array $flashcards): void
    {
        $insert_data = [];
        /** @var Flashcard $flashcard */
        foreach ($flashcards as $flashcard) {
            $insert_data[] = [
                'user_id' => $flashcard->getOwner()->getId(),
                'flashcard_category_id' => $flashcard->getCategoryId()->getValue(),
                'word' => $flashcard->getWord(),
                'word_lang' => $flashcard->getWordLang()->getValue(),
                'translation' => $flashcard->getTranslation(),
                'translation_lang' => $flashcard->getTranslationLang()->getValue(),
                'context' => $flashcard->getContext(),
                'context_translation' => $flashcard->getContextTranslation(),
            ];
        }
        $this->db::table('flashcards')->insert($insert_data);
    }

    public function map(object $data): Flashcard
    {
        return new Flashcard(
            new FlashcardId($data->id),
            $data->word,
            Language::from($data->word_lang),
            $data->translation,
            Language::from($data->translation_lang),
            $data->context,
            $data->context_translation,
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            new CategoryId($data->flashcard_category_id),
        );
    }
}
