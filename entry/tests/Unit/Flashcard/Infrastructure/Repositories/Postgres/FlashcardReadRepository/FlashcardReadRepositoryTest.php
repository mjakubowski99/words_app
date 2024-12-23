<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardReadRepository;

use Tests\Base\FlashcardTestCase;
use Shared\Enum\GeneralRatingType;
use Flashcard\Domain\Models\Rating;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardReadRepository;

class FlashcardReadRepositoryTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardReadRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardReadRepository::class);
    }

    public function test__findRatingStats_returnOnlyUserRatings(): void
    {
        // GIVEN
        $user = $this->createUser();
        $this->createLearningSessionFlashcard([
            'rating' => Rating::GOOD,
            'flashcard_id' => $this->createFlashcard(['user_id' => $user->id])->id,
        ]);
        $this->createLearningSessionFlashcard([
            'rating' => Rating::WEAK,
            'flashcard_id' => $this->createFlashcard()->id,
        ]);
        $expecteds = [
            ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
            ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 0.0],
            ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 100.0],
            ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 0.0],
        ];

        // WHEN
        $results = $this->repository->findStatsByUser($user->getId());

        // THEN
        $i = 0;
        foreach ($results->getRatingStats() as $result) {
            $this->assertSame($expecteds[$i]['rating'], $result->getRating()->getValue()->value);
            $this->assertSame($expecteds[$i]['rating_percentage'], round($result->getRatingPercentage(), 2));
            ++$i;
        }
    }

    /**
     * @dataProvider ratedFlashcardsRatingProvider
     */
    public function test__findRatingStats_returnCorrectValues(
        array $flashcards,
        array $expecteds
    ): void {
        // GIVEN
        $user = $this->createUser();
        foreach ($flashcards as $flashcard) {
            $this->createLearningSessionFlashcard([
                'rating' => $flashcard['rating'],
                'flashcard_id' => $this->createFlashcard(['user_id' => $user->id])->id,
            ]);
        }

        // WHEN
        $results = $this->repository->findStatsByUser($user->getId());

        // THEN
        $i = 0;
        foreach ($results->getRatingStats() as $result) {
            $this->assertSame($expecteds[$i]['rating'], $result->getRating()->getValue()->value);
            $this->assertSame($expecteds[$i]['rating_percentage'], round($result->getRatingPercentage(), 2));
            ++$i;
        }
    }

    public static function ratedFlashcardsRatingProvider(): array
    {
        return [
            'case 1' => [
                'flashcards' => [
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::VERY_GOOD],
                ],
                'expecteds' => [
                    ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 0.0],
                    ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 0.0],
                    ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 66.67],
                    ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 33.33],
                ],
            ],
            'case 2' => [
                'flashcards' => [
                    ['rating' => Rating::UNKNOWN],
                    ['rating' => Rating::WEAK],
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::VERY_GOOD],
                ],
                'expecteds' => [
                    ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 25.0],
                    ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 25.0],
                    ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 25.0],
                    ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 25.0],
                ],
            ],
            'case 3' => [
                'flashcards' => [
                    ['rating' => Rating::UNKNOWN],
                    ['rating' => Rating::WEAK],
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::VERY_GOOD],
                ],
                'expecteds' => [
                    ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 20.0],
                    ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 20.0],
                    ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 40.0],
                    ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 20.0],
                ],
            ],
            'case 4' => [
                'flashcards' => [
                    ['rating' => Rating::UNKNOWN],
                    ['rating' => Rating::WEAK],
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::GOOD],
                    ['rating' => Rating::VERY_GOOD],
                    ['rating' => Rating::VERY_GOOD],
                ],
                'expecteds' => [
                    ['rating' => GeneralRatingType::UNKNOWN->value, 'rating_percentage' => 16.67],
                    ['rating' => GeneralRatingType::WEAK->value, 'rating_percentage' => 16.67],
                    ['rating' => GeneralRatingType::GOOD->value, 'rating_percentage' => 33.33],
                    ['rating' => GeneralRatingType::VERY_GOOD->value, 'rating_percentage' => 33.33],
                ],
            ],
        ];
    }
}
