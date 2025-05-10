<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardPoll;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\LeitnerLevelUpdate;
use Flashcard\Domain\Types\FlashcardIdCollection;

class FlashcardPollMapper
{
    public function __construct(private readonly DB $db) {}

    public function findByUser(UserId $user_id, int $learnt_cards_purge_limit): ?FlashcardPoll
    {
        $flashcards = $this->db::table('flashcard_poll_items')
            ->where('user_id', $user_id->getValue())
            ->whereColumn('easy_ratings_count', '>=', 'easy_ratings_count_to_purge')
            ->take($learnt_cards_purge_limit)
            ->get()
            ->all();

        $count = $this->db::table('flashcard_poll_items')->where('user_id', $user_id->getValue())->count();

        $flashcards_to_reject = array_map(fn (object $data) => new FlashcardId($data->flashcard_id), $flashcards);

        return new FlashcardPoll(
            $user_id,
            $count,
            FlashcardIdCollection::fromArray($flashcards_to_reject),
        );
    }

    public function purgeLatestFlashcards(UserId $user_id, int $limit): void
    {
        $ids = $this->db::table('flashcard_poll_items')
            ->where('user_id', $user_id->getValue())
            ->take($limit)
            ->latest()
            ->pluck('id');

        $this->db::table('flashcard_poll_items')->whereIn('id', $ids)->delete();
    }

    public function saveLeitnerLevelUpdate(LeitnerLevelUpdate $update): bool
    {
        $update_array = [
            'leitner_level' => DB::raw("leitner_level+{$update->getLeitnerLevelIncrementStep()}+1"),
            'updated_at' => now(),
        ];

        if ($update->incrementEasyRatingsCount()) {
            $update_array['easy_ratings_count'] = DB::raw('easy_ratings_count+1');
        }

        $this->db::table('flashcard_poll_items')
            ->where('user_id', $update->getUserId())
            ->whereIn('flashcard_id', $update->getFlashcardIds()->getAll())
            ->update($update_array);

        return true;
    }

    public function save(FlashcardPoll $poll): void
    {
        $this->db::table('flashcard_poll_items')
            ->where('user_id', $poll->getUserId())
            ->whereIn('flashcard_id', $poll->getFlashcardIdsToPurge()->getAll())
            ->delete();

        $insert_data = [];

        if (count($poll->getFlashcardIdsToAdd()) > 0) {
            $min_level = $this->db::table('flashcard_poll_items')
                ->where('user_id', $poll->getUserId())
                ->min('leitner_level') ?? 0;
        } else {
            $min_level = 0;
        }

        foreach ($poll->getFlashcardIdsToAdd() as $flashcard_id) {
            $insert_data[] = [
                'user_id' => $poll->getUserId(),
                'flashcard_id' => $flashcard_id,
                'easy_ratings_count' => 0,
                'easy_ratings_count_to_purge' => $poll->getEasyRepetitionsCountToPurge(),
                'leitner_level' => $min_level,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->db::table('flashcard_poll_items')->insert($insert_data);
    }

    public function selectNextLeitnerFlashcard(UserId $user_id, array $exclude_flashcard_id, int $limit): array
    {
        return $this->db::table('flashcard_poll_items')
            ->whereNotIn('flashcard_id', $exclude_flashcard_id)
            ->where('user_id', $user_id)
            ->orderBy('leitner_level')
            ->orderBy('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (object $data) {
                return new FlashcardId($data->flashcard_id);
            })->all();
    }

    public function resetLeitnerLevelIfMaxLevelExceeded(UserId $user_id, int $max_level): void
    {
        $current_max = $this->db::table('flashcard_poll_items')
            ->where('user_id', $user_id)
            ->max('leitner_level');

        if ($current_max > $max_level) {
            $this->db::table('flashcard_poll_items')
                ->where('user_id', $user_id)
                ->update(['leitner_level' => 0]);
        }
    }
}
