<?php

namespace Flashcard\Infrastructure\Http\Request;

use Flashcard\Application\Command\FlashcardRating;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\SessionId;
use Shared\Http\Request\Request;
use Shared\Utils\ValueObjects\UserId;

class RateSessionFlashcardRequest extends Request
{
    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer'],
            'ratings' => ['array'],
            'ratings.*.session_flashcard_id' => ['required', 'integer'],
            'ratings.*.rating' => ['required', 'integer'],
        ];
    }

    public function getUserId(): UserId
    {
        return $this->current()->getId();
    }

    public function getSessionId(): SessionId
    {
        return new SessionId($this->input('session_id'));
    }

    public function getRatings(): array
    {
        $ratings = [];
        foreach ($this->input('ratings') as $rating) {
            $ratings[] = new FlashcardRating(
                new SessionFlashcardId($rating['session_flashcard_id']),
                Rating::from($rating['rating'])
            );
        }
        return $ratings;
    }
}