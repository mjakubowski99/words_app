<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardResource\Pages;

use Admin\Resources\FlashcardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFlashcard extends CreateRecord
{
    protected static string $resource = FlashcardResource::class;
}
