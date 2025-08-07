<?php

namespace App\Filament\Resources\ScheduleAbsenceResource\Pages;

use App\Filament\Resources\ScheduleAbsenceResource;
use App\Models\Absence;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduleAbsence extends CreateRecord
{
    public function getTitle(): string
    {
        return 'Buat Jadwal Attendance';
    }

    protected static string $resource = ScheduleAbsenceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        
        $this->manPowerId = $data['manpower_user_id'];
        $this->inputLongLat = $data['input_long_lat'];

        
        

        if (is_array($data['input_long_lat'])) {
            // Assuming ['lat' => ..., 'lng' => ...]
            $data['long_lat'] = $data['input_long_lat']['lat'] . ',' . $data['input_long_lat']['lng'];
        }

        $data['name'] = auth()->user()->name; // Set the name to the authenticated user's name

        unset($data['manpower_user_id']);
        unset($data['input_long_lat']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->manpower()->create([
            'user_id' => $this->manPowerId,
        ]);
    }



    
}
