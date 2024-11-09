<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FlashcardCategory;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use App\Console\Traits\EnsureDatabaseDriver;
use Flashcard\Application\Command\MergeFlashcardCategoriesHandler;

class MergeFlashcardsWithSameCategoryCommand extends Command
{
    use EnsureDatabaseDriver;

    protected $signature = 'app:merge-flashcards-with-same-category-command';

    protected $description = 'This command merges flashcards with same category!';

    public function handle(MergeFlashcardCategoriesHandler $handler): void
    {
        $this->ensureDefaultDriverIsPostgres();

        FlashcardCategory::query()
            ->groupByRaw('LOWER(name), user_id')
            ->havingRaw('COUNT(id) > 1')
            ->selectRaw('LOWER(name) as name, user_id, MIN(id) as id')
            ->chunkById(1000, function ($categories) use ($handler) {
                foreach ($categories as $category) {
                    $this->mergeDuplicates($category->name, $category->user_id, $handler);
                }
            });
    }

    private function mergeDuplicates(string $category_name, string $user_id, MergeFlashcardCategoriesHandler $handler): void
    {
        $categories = FlashcardCategory::query()
            ->where('user_id', $user_id)
            ->whereRaw('LOWER(name) = ?', [$category_name])
            ->get();

        if ($categories->count() < 2) {
            return;
        }

        $selected_category = $categories[0];

        for ($i = 1; $i < count($categories); ++$i) {
            $from_category = $categories[$i]->getId();
            $to_category = $selected_category->getId();

            $owner = new Owner(new OwnerId($selected_category->user_id), FlashcardOwnerType::USER);

            $handler->handle($owner, $from_category, $to_category);
        }
    }
}
