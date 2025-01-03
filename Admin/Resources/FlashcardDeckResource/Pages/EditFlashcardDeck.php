<?php

declare(strict_types=1);

namespace Admin\Resources\FlashcardDeckResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Admin\Resources\FlashcardDeckResource;
use Admin\Traits\HasFlashcardConfigurator;

class EditFlashcardDeck extends EditRecord
{
    use HasFlashcardConfigurator;

    protected static string $resource = FlashcardDeckResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
