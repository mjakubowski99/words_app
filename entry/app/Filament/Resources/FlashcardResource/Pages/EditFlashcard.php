<?php

namespace App\Filament\Resources\FlashcardResource\Pages;

use App\Filament\Resources\FlashcardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlashcard extends EditRecord
{
    protected static string $resource = FlashcardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
