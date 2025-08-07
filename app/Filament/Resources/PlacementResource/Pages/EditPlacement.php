<?php

namespace App\Filament\Resources\PlacementResource\Pages;

use App\Filament\Resources\PlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlacement extends EditRecord
{
    protected static string $resource = PlacementResource::class;

     protected function mutateFormDataBeforeFill(array $data): array
    {
        // Make sure the relation is loaded

        $this->record->loadMissing(['user', 'project']);

        
        $data['project_id'] = $this->record->project?->project_id;

        

        return $data;
    }

 protected function mutateFormDataBeforeSave(array $data): array
    {

        $this->inputLongLat = $data['input_long_lat'];

        
        

        if (is_array($data['input_long_lat'])) {
            // Assuming ['lat' => ..., 'lng' => ...]
            $data['long_lat'] = $data['input_long_lat']['lat'] . ',' . $data['input_long_lat']['lng'];
        }
        
unset($data['project_id']);
        unset($data['input_long_lat']);
        return $data;
    }

    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
