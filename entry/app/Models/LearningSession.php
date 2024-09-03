<?php

namespace App\Models;

use Flashcard\Domain\Models\SessionId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningSession extends Model
{
    use HasFactory;

    public function getId(): SessionId
    {
        return new SessionId($this->id);
    }
}
