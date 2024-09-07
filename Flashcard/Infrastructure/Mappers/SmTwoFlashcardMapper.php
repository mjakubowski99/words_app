<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;

class SmTwoFlashcardMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function create(SmTwoFlashcard $flashcard): void
    {
        $this->db::table('sm_two_flashcards')
            ->insert([
                'flashcard_id' => $flashcard->getFlashcardId(),
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
                'sm_two_flashcards.flashcard_id',
                'sm_two_flashcards.user_id',
                'sm_two_flashcards.repetition_interval',
                'sm_two_flashcards.repetition_ratio',
                'sm_two_flashcards.repetition_count',
            )
            ->get()
            ->map(function (object $sm_two_flashcard) {
                return $this->map($sm_two_flashcard);
            })->toArray();

        return new SmTwoFlashcards($results);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        foreach ($sm_two_flashcards->all() as $flashcard) {
            $this->db::table('sm_two_flashcards')
                ->updateOrInsert([
                    'flashcard_id' => $flashcard->getFlashcardId(),
                    'user_id' => $flashcard->getUserId(),
                ], [
                    'repetition_ratio' => $flashcard->getRepetitionRatio(),
                    'repetition_interval' => $flashcard->getRepetitionInterval(),
                    'repetition_count' => $flashcard->getRepetitionCount(),
                ]);
        }
    }

    private function map(object $data): SmTwoFlashcard
    {
        return new SmTwoFlashcard(
            new UserId($data->user_id),
            new FlashcardId($data->flashcard_id),
            (float) $data->repetition_ratio,
            (float) $data->repetition_interval,
            $data->repetition_count,
        );
    }
}
