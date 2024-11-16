<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\FlashcardDeck;
use Illuminate\Console\Command;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use App\Console\Traits\EnsureDatabaseDriver;
use Flashcard\Application\Command\MergeFlashcardDecksHandler;

class MergeFlashcardsWithSameDeckCommand extends Command
{
    use EnsureDatabaseDriver;

    protected $signature = 'app:merge-flashcards-with-same-deck-command';

    protected $description = 'This command merges flashcards with same decks!';

    public function handle(MergeFlashcardDecksHandler $handler): void
    {
        $this->ensureDefaultDriverIsPostgres();

        FlashcardDeck::query()
            ->groupByRaw('LOWER(name), user_id')
            ->havingRaw('COUNT(id) > 1')
            ->selectRaw('LOWER(name) as name, user_id, MIN(id) as id')
            ->chunkById(1000, function ($decks) use ($handler) {
                foreach ($decks as $deck) {
                    $this->mergeDuplicates($deck->name, $deck->user_id, $handler);
                }
            });
    }

    private function mergeDuplicates(string $deck_name, string $user_id, MergeFlashcardDecksHandler $handler): void
    {
        $decks = FlashcardDeck::query()
            ->where('user_id', $user_id)
            ->whereRaw('LOWER(name) = ?', [$deck_name])
            ->get();

        if ($decks->count() < 2) {
            return;
        }

        $selected_deck = $decks[0];

        for ($i = 1; $i < count($decks); ++$i) {
            $from_deck = $decks[$i]->getId();
            $to_deck = $selected_deck->getId();

            $owner = new Owner(new OwnerId($selected_deck->user_id), FlashcardOwnerType::USER);

            $handler->handle($owner, $from_deck, $to_deck);
        }
    }
}
