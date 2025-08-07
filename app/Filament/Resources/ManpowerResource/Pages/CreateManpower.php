<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Request;

class CreateManpower extends CreateRecord
{
    protected static string $resource = ManpowerResource::class;

    protected int|string|null $projectId = null;
    protected int|string|null $groupId = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->groupId = $data['group_id'];
        $this->projectId = $data['project'];
        unset($data['group_id']);
        unset($data['project']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create the related group
        $this->record->group()->create([
            'group_id' => $this->groupId,
        ]);

        $this->record->manpower()->create([
            'project_id' => $this->projectId,
        ]);

        // Assign "man_power" role to the created user
        $this->record->assignRole('man_power');
    }

    protected function getRedirectUrl(): string
    {
        // Redirect back to the index page with the project parameter
        return $this->getResource()::getUrl('index', ['project' => $this->projectId]);
    }
}