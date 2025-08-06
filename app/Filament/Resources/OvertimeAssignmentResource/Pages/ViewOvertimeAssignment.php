<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use App\Models\ScheduleAbsence;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Notification as Notifications;


class ViewOvertimeAssignment extends ViewRecord
{
    protected static string $resource = OvertimeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-m-check-badge')
                ->visible(fn() => $this->record->status === 'waiting' && auth()->user()?->can('update_overtime::assignment'))
                ->requiresConfirmation()
                ->action(function () {
                    dd($this->record->projectuser);
                    $this->record->update(['status' => 'approved']);
                    Notification::make()
                        ->title('Disetujui')
                        ->success()
                        ->body('Lembur berhasil disetujui.')
                        ->send();
                    $schedule = ScheduleAbsence::whereDate('absence_date', '>=', $this->record->start_time)
                        ->whereDate('absence_date', '<=', $this->record->end_time)
                        ->where('user.user.id', $this->record->projectuser->user_id) // Optional: match by user
                        ->update([
                        'status' => json_decode($this->record->description)->submission_type, // Example field update
                        ]);
                    dd($schedule);
                }),

            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-m-x-circle')
                ->visible(fn() => $this->record->status === 'waiting' && auth()->user()?->can('update_overtime::assignment'))
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'rejected']);
                    Notification::make()
                        ->title('Ditolak')
                        ->success()
                        ->body('Lembur telah ditolak.')
                        ->send();
                }),
        ];
    }
 
}
