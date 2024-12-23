<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SmTwoFlashcard;
use Illuminate\Console\Command;
use App\Models\LearningSessionFlashcard;

class FillSmTwoMaxRatingCommand extends Command
{
    protected $signature = 'app:fill-sm-two-max-rating-command';

    protected $description = 'Fill sm two flashcard max rating';

    public function handle(): void
    {
        SmTwoFlashcard::query()
            ->limit(2000)
            ->get()
            ->each(function (SmTwoFlashcard $flashcard) {
                $min_rating = LearningSessionFlashcard::query()
                    ->leftJoin(
                        'learning_sessions',
                        'learning_sessions.id',
                        '=',
                        'learning_session_flashcards.learning_session_id'
                    )
                    ->where('user_id', $flashcard->user_id)
                    ->where('flashcard_id', $flashcard->id)
                    ->min('rating');

                if (!$min_rating) {
                    $flashcard->min_rating = 0;
                } else {
                    $flashcard->min_rating = $min_rating;
                }
                $flashcard->save();
            });
    }
}
