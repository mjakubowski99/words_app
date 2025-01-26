<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;

class FlashcardDuplicateRepository implements IFlashcardDuplicateRepository
{
    public function __construct(private DB $db) {}

    public function getAlreadySavedFrontWords(FlashcardDeckId $deck_id, array $front_words): array
    {
        return $this->db::table('flashcards')
            ->whereIn(DB::raw('LOWER(front_word)'), array_map(fn ($word) => mb_strtolower($word), $front_words))
            ->where('flashcard_deck_id', $deck_id->getValue())
            ->pluck('front_word')
            ->toArray();
    }

    public function getRandomFrontWordInitialLetters(FlashcardDeckId $deck_id, int $limit): array
    {
        $array = $this->db::table('flashcards')
            ->limit($limit)
            ->inRandomOrder()
            ->where('flashcard_deck_id', $deck_id->getValue())
            ->selectRaw('front_word')
            ->pluck('front_word')
            ->map(function (string $front_word) {
                return mb_substr($front_word, 0, 1);
            })->toArray();

        return array_values(array_unique($array));
    }
}
