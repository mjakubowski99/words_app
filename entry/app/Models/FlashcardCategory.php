<?php

namespace App\Models;

use Flashcard\Domain\Models\CategoryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashcardCategory extends Model
{
    use HasFactory;

    protected $table = 'flashcard_categories';

    public function getId(): CategoryId
    {
        return new CategoryId($this->id);
    }
}
