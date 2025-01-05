<?php

namespace Admin\Resources;

use Admin\Resources\ReportResource\Pages;
use Admin\Models\Report;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Shared\Enum\ReportableType;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(function (Report $report) {
                        return Str::of($report->type)->replace('_', ' ')->title();
                    }),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('description')->wrap(),
                Tables\Columns\TextColumn::make('reportable_type')
                    ->label('Reported resource')
                    ->formatStateUsing(function (Report $report) {
                        if (!$report->getReportableType() || $report->getReportableType() === ReportableType::UNKNOWN) {
                            return null;
                        }

                        $link = match ($report->getReportableType()) {
                            ReportableType::FLASHCARD => FlashcardResource::getUrl('edit', [$report->getReportableId()]),
                            default => '#',
                        };

                        return '<a href="' . e($link) . '" target="_blank" class="text-blue-500 underline">' . $link . '</a>';
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }
}
