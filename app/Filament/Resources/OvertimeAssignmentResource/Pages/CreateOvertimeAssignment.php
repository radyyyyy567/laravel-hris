<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertimeAssignment extends CreateRecord
{
    protected static string $resource = OvertimeAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $today = now()->toDateString(); // contoh: 2025-06-25

        $data['start_time'] = Carbon::parse("$today {$data['start_time']}");
        $data['end_time'] = Carbon::parse("$today {$data['end_time']}");
        $this->projectId = $data['project_id'];

        unset($data['project_id']);
        return $data;
    }

    protected function afterCreate(): void
    {

        $this->record->projectuser()->create([
            'user_id' => auth()->id(),
            'project_id' => $this->projectId
        ]);

    }


    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('create_overtime::assignment');
    }

}
