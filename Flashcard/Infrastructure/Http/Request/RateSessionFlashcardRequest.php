<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Application\Command\FlashcardRating;

#[OAT\Schema(
    schema: 'Requests\Flashcard\RateSessionFlashcardRequest',
    required: ['ratings'],
    properties: [
        new OAT\Property(
            property: 'ratings',
            description: 'List of ratings for flashcards',
            type: 'array',
            items: new OAT\Items(
                properties: [
                    new OAT\Property(
                        property: 'id',
                        description: 'ID of the flashcard in the session',
                        type: 'integer',
                        example: 12,
                    ),
                    new OAT\Property(
                        property: 'rating',
                        description: 'Rating given to the flashcard',
                        type: 'integer',
                        enum: [Rating::UNKNOWN, Rating::WEAK, Rating::GOOD, Rating::VERY_GOOD],
                        example: Rating::GOOD,
                    ),
                ],
                type: 'object'
            )
        ),
    ]
)]
class RateSessionFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'ratings' => ['array'],
            'ratings.*.id' => ['required', 'integer'],
            'ratings.*.rating' => ['required', 'integer'],
        ];
    }

    public function getUserId(): UserId
    {
        return $this->current()->getId();
    }

    public function getSessionId(): SessionId
    {
        return new SessionId((int) $this->route('session_id'));
    }

    public function getRatings(): array
    {
        $ratings = [];
        foreach ($this->input('ratings') as $rating) {
            $ratings[] = new FlashcardRating(
                new SessionFlashcardId($rating['id']),
                Rating::from($rating['rating'])
            );
        }

        return $ratings;
    }
}
