<?php

namespace App\Filament\Resources\FlashcardDeckResource\Pages;

use App\Filament\Resources\FlashcardDeckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlashcardDeck extends EditRecord
{
    protected static string $resource = FlashcardDeckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
