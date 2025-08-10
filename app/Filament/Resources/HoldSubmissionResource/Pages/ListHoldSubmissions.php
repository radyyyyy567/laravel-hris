<?php

namespace App\Filament\Resources\HoldSubmissionResource\Pages;

use App\Filament\Resources\HoldSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHoldSubmissions extends ListRecords
{
    protected static string $resource = HoldSubmissionResource::class;

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
