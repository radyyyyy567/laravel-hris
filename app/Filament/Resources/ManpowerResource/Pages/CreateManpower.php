<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateManpower extends CreateRecord
{
    protected static string $resource = ManpowerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->groupId = $data['group_id'];
        
        unset($data['group_id']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->group()->create([
            'group_id' => $this->groupId,
        ]);

    }
}
