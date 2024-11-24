<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardId;

class SmTwoFlashcardMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function findMany(Owner $owner, array $flashcard_ids): SmTwoFlashcards
    {
        $results = $this->db::table('sm_two_flashcards')
            ->where('sm_two_flashcards.user_id', $owner->getId())
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
        $now = now();
        $query = $this->db::table('sm_two_flashcards');

        foreach ($sm_two_flashcards->all() as $flashcard) {
            $query->orWhere(
                fn ($q) => $q->where([
                    'flashcard_id' => $flashcard->getFlashcardId(),
                    'user_id' => $flashcard->getOwner()->getId(),
                ])
            );
        }

        $results = $query->select('flashcard_id', 'user_id')->get();

        $insert_data = [];

        foreach ($sm_two_flashcards->all() as $flashcard) {
            if (
                $results->where('flashcard_id', $flashcard->getFlashcardId()->getValue())
                    ->where('user_id', $flashcard->getOwner()->getId()->getValue())
                    ->isEmpty()
            ) {
                $insert_data[] = [
                    'flashcard_id' => $flashcard->getFlashcardId(),
                    'user_id' => $flashcard->getOwner()->getId(),
                    'repetition_ratio' => $flashcard->getRepetitionRatio(),
                    'repetition_interval' => $flashcard->getRepetitionInterval(),
                    'repetition_count' => $flashcard->getRepetitionCount(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } else {
                $this->db::table('sm_two_flashcards')
                    ->where([
                        'flashcard_id' => $flashcard->getFlashcardId(),
                        'user_id' => $flashcard->getOwner()->getId(),
                    ])->update([
                        'repetition_ratio' => $flashcard->getRepetitionRatio(),
                        'repetition_interval' => $flashcard->getRepetitionInterval(),
                        'repetition_count' => $flashcard->getRepetitionCount(),
                        'updated_at' => $now,
                    ]);
            }
        }

        if (!empty($insert_data)) {
            $this->db::table('sm_two_flashcards')->insert($insert_data);
        }
    }

    private function map(object $data): SmTwoFlashcard
    {
        return new SmTwoFlashcard(
            new Owner(new OwnerId($data->user_id), FlashcardOwnerType::USER),
            new FlashcardId($data->flashcard_id),
            (float) $data->repetition_ratio,
            (float) $data->repetition_interval,
            $data->repetition_count,
        );
    }
}
