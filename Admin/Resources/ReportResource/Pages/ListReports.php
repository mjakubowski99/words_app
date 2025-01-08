<?php

declare(strict_types=1);

namespace Admin\Resources\ReportResource\Pages;

use Admin\Resources\ReportResource;
use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
