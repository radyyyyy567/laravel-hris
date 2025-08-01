<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOvertimeAssignments extends ListRecords
{
    protected static string $resource = OvertimeAssignmentResource::class;

    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
