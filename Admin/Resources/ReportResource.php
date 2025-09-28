<?php

declare(strict_types=1);

namespace Admin\Resources;

use Admin\Models\Report;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Shared\Enum\ReportableType;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Admin\Resources\ReportResource\Pages\ListReports;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('type')
                    ->formatStateUsing(function (Report $report) {
                        return Str::of($report->type)->replace('_', ' ')->title();
                    }),
                TextColumn::make('email')->searchable(),
                TextColumn::make('description')->wrap(),
                TextColumn::make('reportable_type')
                    ->label('Reported resource')
                    ->formatStateUsing(function (Report $report) {
                        if (!$report->getReportableType() || $report->getReportableType() === ReportableType::UNKNOWN) {
                            return null;
                        }

                        $link = match ($report->getReportableType()) {
                            /* @phpstan-ignore-next-line */
                            ReportableType::FLASHCARD => FlashcardResource::getUrl('edit', [$report->getReportableId()]),
                            default => '#',
                        };

                        return '<a href="' . e($link) . '" target="_blank" class="text-blue-500 underline">' . $link . '</a>';
                    })
                    ->html(),
                TextColumn::make('created_at'),
            ])
            ->filters([
            ])
            ->recordActions([
            ])
            ->toolbarActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
        ];
    }
}
