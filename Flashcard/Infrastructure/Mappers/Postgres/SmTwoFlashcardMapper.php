<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardId;

class SmTwoFlashcardMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function resetRepetitionsInSession(UserId $user_id): void
    {
        $this->db::table('sm_two_flashcards')
            ->where('user_id', $user_id->getValue())
            ->where('repetitions_in_session', '>', 0)
            ->update(['repetitions_in_session' => 0]);
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
                'sm_two_flashcards.min_rating',
                'sm_two_flashcards.repetitions_in_session',
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
                    'user_id' => $flashcard->getUserId(),
                ])
            );
        }

        $results = $query->select('flashcard_id', 'user_id')->get();

        $insert_data = [];

        foreach ($sm_two_flashcards->all() as $flashcard) {
            if (
                $results->where('flashcard_id', $flashcard->getFlashcardId()->getValue())
                    ->where('user_id', $flashcard->getUserId())
                    ->isEmpty()
            ) {
                $insert_data[] = [
                    'flashcard_id' => $flashcard->getFlashcardId(),
                    'user_id' => $flashcard->getUserId(),
                    'repetition_ratio' => $flashcard->getRepetitionRatio(),
                    'repetition_interval' => $flashcard->getRepetitionInterval() > 10000 ? 10000 : $flashcard->getRepetitionInterval(),
                    'repetition_count' => $flashcard->getRepetitionCount(),
                    'min_rating' => $flashcard->getMinRating(),
                    'repetitions_in_session' => $flashcard->getRepetitionsInSession(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } else {
                $this->db::table('sm_two_flashcards')
                    ->where([
                        'flashcard_id' => $flashcard->getFlashcardId(),
                        'user_id' => $flashcard->getUserId(),
                    ])->update([
                        'repetition_ratio' => $flashcard->getRepetitionRatio(),
                        'repetition_interval' => $flashcard->getRepetitionInterval() > 10000 ? 10000 : $flashcard->getRepetitionInterval(),
                        'repetition_count' => $flashcard->getRepetitionCount(),
                        'min_rating' => $flashcard->getMinRating(),
                        'repetitions_in_session' => $flashcard->getRepetitionsInSession(),
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
            new UserId($data->user_id),
            new FlashcardId($data->flashcard_id),
            (float) $data->repetition_ratio,
            (float) $data->repetition_interval,
            $data->repetition_count,
            $data->min_rating,
            $data->repetitions_in_session
        );
    }
}
