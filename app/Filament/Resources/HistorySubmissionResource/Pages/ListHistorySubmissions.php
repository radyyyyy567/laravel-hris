<?php

namespace App\Filament\Resources\HistorySubmissionResource\Pages;

use App\Filament\Resources\HistorySubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistorySubmissions extends ListRecords
{
    protected static string $resource = HistorySubmissionResource::class;

    public function getTitle(): string
    {
        return 'Pengajuan Hold';
    }
    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
