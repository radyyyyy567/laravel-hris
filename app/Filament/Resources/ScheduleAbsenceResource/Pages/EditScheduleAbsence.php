<?php

namespace App\Filament\Resources\ScheduleAbsenceResource\Pages;

use App\Filament\Resources\ScheduleAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduleAbsence extends EditRecord
{
    protected static string $resource = ScheduleAbsenceResource::class;

public function getTitle(): string
    {
        return 'Edit Jadwal Attendance';
    }
    public function mutateFormDataBeforeSave(array $data): array
    {
        if (is_array($data['long_lat'])) {
            // Assuming ['lat' => ..., 'lng' => ...]
            $data['long_lat'] = $data['long_lat']['lat'] . ',' . $data['long_lat']['lng'];
        }
        
        unset($data['manpower_user_id']);
        return $data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
