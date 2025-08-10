<?php

namespace App\Filament\Resources\SpvResource\Pages;

use App\Filament\Resources\SpvResource;
use App\Models\RelationPlacementUser;
use Filament\Resources\Pages\CreateRecord;

class CreateSpv extends CreateRecord
{
    protected static string $resource = SpvResource::class;

    protected int|string|null $projectId = null;
    protected int|string|null $placementId = null;

    protected array $customFields = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Save needed fields
        $this->projectId = $data['project_id'] ?? null;
        $this->placementId = $data['placement_id'] ?? null;

        // Extract custom fields
        $this->customFields = [
            'group' => $data['group'] ?? null,
            'notelp' => $data['notelp'] ?? null,
            'gender' => $data['gender'] ?? null,
        ];

        // Clean up main data
        unset(
            $data['group'],
            $data['notelp'],
            $data['gender'],
            $data['project_id'],
            $data['placement_id'] // optional: if not used
        );

        // Store custom fields as JSON
        $data['description'] = json_encode($this->customFields);

        // Encrypt password if exists
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create relation to project (manpower)
        $this->record->pic()->create([
            'project_id' => $this->projectId,
        ]);

        // Create placement relationship
        RelationPlacementUser::create([
            'user_id' => $this->record->id,
            'placement_id' => $this->placementId,
        ]);

        // Assign role
        $this->record->assignRole('spv');
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
