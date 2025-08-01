<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOvertimeAssignment extends EditRecord
{
    protected static string $resource = OvertimeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
{
    $today = now()->toDateString();

    if (!empty($data['start_time'])) {
        $data['start_time'] = Carbon::parse("{$today} {$data['start_time']}");
    } else {
        unset($data['start_time']); // don't update it
    }

    if (!empty($data['end_time'])) {
        $data['end_time'] = Carbon::parse("{$today} {$data['end_time']}");
    } else {
        unset($data['end_time']); // don't update it
    }

    return $data;
}

public static function canAccess(array $parameters = []): bool
{
    return auth()->user()?->can('update_overtime::assignment');
}
}
