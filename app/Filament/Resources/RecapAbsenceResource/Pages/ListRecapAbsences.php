<?php

namespace App\Filament\Resources\RecapAbsenceResource\Pages;

use App\Filament\Resources\RecapAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecapAbsences extends ListRecords
{
    protected static string $resource = RecapAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
