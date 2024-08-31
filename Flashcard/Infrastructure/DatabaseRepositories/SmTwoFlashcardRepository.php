<?php

namespace Flashcard\Infrastructure\DatabaseRepositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Infrastructure\DatabaseMappers\FlashcardMapper;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;

class SmTwoFlashcardRepository extends AbstractRepository implements ISmTwoFlashcardRepository
{
    public function __construct(
        private readonly DB $db,
        private FlashcardMapper $flashcard_mapper,
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
                ...$this->dbPrefix('flashcards', FlashcardMapper::COLUMNS),
                ...$this->dbPrefix('sm_two_flashcards', ['repetition_ratio', 'repetition_interval', 'user_id'])
            )
            ->get()
            ->map(function (object $sm_two_flashcard) {
                $flashcard = $this->flashcard_mapper->map((array) $sm_two_flashcard);

                return new SmTwoFlashcard(
                    UserId::fromString($sm_two_flashcard->user_id),
                    $flashcard,
                    $sm_two_flashcard->repetition_ratio,
                    $sm_two_flashcard->repetition_interval
                );
            })->toArray();

        return new SmTwoFlashcards($results);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        $insert_data = [];
        foreach ($sm_two_flashcards->all() as $flashcard) {
            $insert_data[] = [
                'flashcard_id' => $flashcard->getFlashcardId(),
                'user_id' => $flashcard->getUserId(),
                'repetition_ratio' => $flashcard->getRepetitionRatio(),
                'repetition_interval' => $flashcard->getRepetitionInterval(),
            ];
        }

        $this->db::table('sm_two_flashcards')->updateOrInsert($insert_data, ['flashcard_id', 'user_id']);
    }

    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, CategoryId $category_id, int $limit): array
    {
        return $this->db::table('sm_two_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'sm_two_flashcards.flashcard_id')
            ->where('flashcards.flashcard_category_id', $category_id->getValue())
            ->orderBy('repetition_interval', 'ASC')
            ->take($limit)
            ->get()
            ->map(function (object $sm_two_flashcard) {
                return $this->flashcard_mapper->map((array) $sm_two_flashcard);
            })->toArray();
    }
}