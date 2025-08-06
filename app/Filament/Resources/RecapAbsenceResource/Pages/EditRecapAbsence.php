<?php

namespace App\Filament\Resources\RecapAbsenceResource\Pages;

use App\Filament\Resources\RecapAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecapAbsence extends EditRecord
{
    protected static string $resource = RecapAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
