<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Resources\Pages\EditRecord;

class EditManpower extends EditRecord
{
    protected static string $resource = ManpowerResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->groupId = $data['group_id'];


         if (empty($data['password'])) {
            unset($data['password']); // don't update it if left blank
        } else {
            $data['password'] = bcrypt($data['password']); // hash if filled
        }

        
        unset($data['group_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Optional: delete old group if needed
        $this->record->group()->delete();

        $this->record->group()->create([
            'group_id' => $this->groupId,
        ]);
    }
}
