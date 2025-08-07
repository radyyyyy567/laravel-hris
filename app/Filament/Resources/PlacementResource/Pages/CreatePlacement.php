<?php

namespace App\Filament\Resources\PlacementResource\Pages;

use App\Filament\Resources\PlacementResource;
use App\Models\ProjectManpower;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlacement extends CreateRecord
{
    protected static string $resource = PlacementResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {

        $this->inputLongLat = $data['input_long_lat'];
        
        $this->manPowerIds = $data['manpower_user_ids'];
        $this->projectId = $data['project_id'];
        
        
        

        if (is_array($data['input_long_lat'])) {
            // Assuming ['lat' => ..., 'lng' => ...]
            $data['long_lat'] = $data['input_long_lat']['lat'] . ',' . $data['input_long_lat']['lng'];
        }

        unset($data['manpower_user_ids']);
        unset($data['project_id']);
        unset($data['input_long_lat']);
        return $data;
    }

     protected function afterCreate(): void
    {

        foreach ($this->manPowerIds as $userId) {
            $this->record->user()->create([
                'user_id' => $userId,
            ]);
        };

       foreach ($this->manPowerIds as $userId) {
          ProjectManpower::create([
            'project_id' => $this->projectId, // make sure $this->project_id exists
            'user_id' => $userId,
        ]);
        }

        $this->record->project()->create([
            'project_id' => $this->projectId,
        ]);
    }
}
