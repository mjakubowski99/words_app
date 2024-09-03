<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Infrastructure\Repositories\Mappers\FlashcardMapper;
use Flashcard\Infrastructure\Repositories\Mappers\SmTwoFlashcardMapper;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;

class SmTwoFlashcardRepository implements ISmTwoFlashcardRepository
{
    public function __construct(
        private readonly DB $db,
        private FlashcardMapper $flashcard_mapper,
        private SmTwoFlashcardMapper $sm_two_flashcard_mapper,
    ) {}

    public function create(SmTwoFlashcard $flashcard): void
    {
        $this->db::table('sm_two_flashcards')
            ->insertGetId([
                'flashcard_id' => $flashcard->getFlashcard()->getId(),
                'user_id' => $flashcard->getUserId(),
                'repetition_ratio' => $flashcard->getRepetitionRatio(),
                'repetition_interval' => $flashcard->getRepetitionInterval(),
            ]);
    }

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards
    {
        $results = $this->db::table('sm_two_flashcards')
            ->where('sm_two_flashcards.user_id', $user_id->getValue())
            ->whereIn('sm_two_flashcards.flashcard_id', $flashcard_ids)
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->select(
                'flashcards.id as flashcards_id',
                'flashcards.word as flashcards_word',
                'flashcards.word_lang as flashcards_word_lang',
                'flashcards.translation as flashcards_translation',
                'flashcards.translation_lang as flashcards_translation_lang',
                'flashcards.context as flashcards_context',
                'flashcards.context_translation as flashcards_context_translation',
                'sm_two_flashcards.user_id as sm_two_flashcards_user_id',
                'sm_two_flashcards.repetition_interval as sm_two_flashcards_repetition_interval',
                'sm_two_flashcards.repetition_ratio as sm_two_flashcards_repetition_ratio',
                'sm_two_flashcards.repetition_count as sm_two_flashcards_repetition_count',
            )
            ->get()
            ->map(function (object $sm_two_flashcard) {
                return $this->sm_two_flashcard_mapper->map((array) $sm_two_flashcard);
            })->toArray();

        return new SmTwoFlashcards($results);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        foreach ($sm_two_flashcards->all() as $flashcard) {
            $this->db::table('sm_two_flashcards')
                ->updateOrInsert([
                    'flashcard_id' => $flashcard->getFlashcard()->getId(),
                    'user_id' => $flashcard->getUserId(),
                ], [
                    'repetition_ratio' => $flashcard->getRepetitionRatio(),
                    'repetition_interval' => $flashcard->getRepetitionInterval(),
                    'repetition_count' => $flashcard->getRepetitionCount(),
                ]);
        }
    }

    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, CategoryId $category_id, int $limit): array
    {
        return $this->db::table('sm_two_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->where('flashcards.flashcard_category_id', $category_id->getValue())
            ->orderBy('sm_two_flashcards.repetition_interval', 'ASC')
            ->take($limit)
            ->select(
                'flashcards.id as flashcards_id',
                'flashcards.word as flashcards_word',
                'flashcards.word_lang as flashcards_word_lang',
                'flashcards.translation as flashcards_translation',
                'flashcards.translation_lang as flashcards_translation_lang',
                'flashcards.context as flashcards_context',
                'flashcards.context_translation as flashcards_context_translation',
            )
            ->get()
            ->map(function (object $sm_two_flashcard) {
                return $this->flashcard_mapper->map((array) $sm_two_flashcard);
            })->toArray();
    }
}