<?php

declare(strict_types=1);

namespace Admin\Resources\UserResource\Pages;

use App\Models\User;
use Admin\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getTableQuery(): Builder
    {
        return User::query()->withCount('flashcards');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
