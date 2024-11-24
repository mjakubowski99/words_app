<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\FlashcardRead;
use Flashcard\Application\ReadModels\GeneralRating;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;

class FlashcardCategoryReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findDetails(FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        $deck = $this->db::table('flashcard_decks')->find($id->getValue());

        if (!$deck) {
            throw new ModelNotFoundException('Category not found');
        }

        $results = $this->db::table('flashcards')
            ->latest()
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $search = mb_strtolower($search);

                    return $q->where(DB::raw('LOWER(flashcards.front_word)'), 'LIKE', '%' . $search . '%')
                        ->orWhere(DB::raw('LOWER(flashcards.back_word)'), 'LIKE', '%' . $search . '%');
                });
            })
            ->where('flashcards.flashcard_deck_id', $id->getValue())
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->selectRaw('
                flashcards.*,
                (SELECT learning_session_flashcards.rating
                 FROM learning_session_flashcards 
                 WHERE flashcards.id = learning_session_flashcards.flashcard_id
                 ORDER BY learning_session_flashcards.updated_at DESC
                 LIMIT 1
                ) as last_rating
            ')
            ->get()
            ->map(function (object $data) {
                return new FlashcardRead(
                    new FlashcardId($data->id),
                    $data->front_word,
                    Language::from($data->front_lang),
                    $data->back_word,
                    Language::from($data->back_lang),
                    $data->front_context,
                    $data->back_context,
                    new GeneralRating($data->last_rating)
                );
            })->toArray();

        return new DeckDetailsRead(
            new FlashcardDeckId($deck->id),
            $deck->name,
            $results
        );
    }

    public function getByOwner(Owner $owner, ?string $search, int $page, int $per_page): array
    {
        return $this->db::table('flashcard_decks')
            ->where('flashcard_decks.user_id', $owner->getId())
            ->when(!is_null($search), function ($query) use ($search) {
                return $query->where(DB::raw('LOWER(flashcard_decks.name)'), 'LIKE', '%' . mb_strtolower($search) . '%');
            })
            ->take($per_page)
            ->skip(($page - 1) * $per_page)
            ->latest()
            ->get()
            ->map(function (object $data) {
                return new OwnerCategoryRead(
                    new FlashcardDeckId($data->id),
                    $data->name,
                );
            })->toArray();
    }
}
