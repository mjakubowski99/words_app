<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardResource\Pages;

use Admin\Resources\FlashcardResource;
use Filament\Resources\Pages\EditRecord;
use Admin\Traits\HasFlashcardConfigurator;

class EditFlashcard extends EditRecord
{
    use HasFlashcardConfigurator;

    protected static string $resource = FlashcardResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
