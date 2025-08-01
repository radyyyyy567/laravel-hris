<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManpower extends EditRecord
{
    protected static string $resource = ManpowerResource::class;

protected function mutateFormDataBeforeSave(array $data): array
{
    
    // If password is empty, remove it so it won't overwrite
    if (empty($data['password'])) {
        unset($data['password']);
    }
    return $data;
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
