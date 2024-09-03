<?php

namespace App\Models;

use Flashcard\Domain\Models\SessionFlashcardId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningSessionFlashcard extends Model
{
    use HasFactory;

    public function getId(): SessionFlashcardId
    {
        return new SessionFlashcardId($this->id);
    }
}
