<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;

class FlashcardFromSmTwoMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function getFlashcardsWithLowestRepetitionInterval(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('sm_two_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->where('sm_two_flashcards.user_id', $owner->getId())
            ->whereNotIn('sm_two_flashcards.flashcard_id', $exclude_flashcard_ids)
            ->orderBy('sm_two_flashcards.repetition_interval', 'ASC')
            ->take($limit)
            ->select('id', 'word', 'word_lang', 'translation', 'translation_lang', 'context', 'context_translation', 'flashcards.user_id', 'flashcards.flashcard_category_id')
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    public function getFlashcardsWithLowestRepetitionIntervalByCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->db::table('sm_two_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->where('flashcards.flashcard_category_id', $category_id->getValue())
            ->whereNotIn('sm_two_flashcards.flashcard_id', $exclude_flashcard_ids)
            ->orderBy('sm_two_flashcards.repetition_interval', 'ASC')
            ->take($limit)
            ->select('id', 'word', 'word_lang', 'translation', 'translation_lang', 'context', 'context_translation', 'flashcards.user_id', 'flashcards.flashcard_category_id')
            ->get()
            ->map(function (object $flashcard) {
                return $this->map($flashcard);
            })->toArray();
    }

    private function map(object $data): Flashcard
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
            null,
        );
    }
}
