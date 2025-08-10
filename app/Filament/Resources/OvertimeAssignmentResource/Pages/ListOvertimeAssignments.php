<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOvertimeAssignments extends ListRecords
{
    protected static string $resource = OvertimeAssignmentResource::class;

     public function getTitle(): string
    {
        return 'Pengajuan';
    }

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
