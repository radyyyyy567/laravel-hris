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
    public function getTitle(): string
    {
        return 'Detail Pengajuan';
    }
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
                    // Remove dd() - it stops execution
        

                    // Check if description exists and is valid JSON
                    if ($this->record->description && $description = json_decode($this->record->description)) {
                        if (isset($description->submission_type) && $description->submission_type === 'cuti') {
                            // Query ScheduleAbsence through the manpower relationship
                            $records = ScheduleAbsence::with('manpower.user') // eager load manpower and user
                                ->whereDate('absence_date', '>=', $this->record->start_time)
                                ->whereDate('absence_date', '<=', $this->record->end_time)
                                ->whereHas('manpower.user', function ($query) {
                                $query->where('id', $this->record->projectuser->first()->user_id);})
                                ->update([
                                    'status' => $description->submission_type,
                                ]);
                            

                            // Log or handle the result instead of dd()
                            // if ($affectedRows > 0) {
                            //     \Log::info("Updated {$affectedRows} schedule absence records");
                            // }
                        }
                    }

                    

                    $this->record->update(['status' => 'approved']);
        
                    Notification::make()
                        ->title('Disetujui')
                        ->success()
                        ->body('Lembur berhasil disetujui.')
                        ->send();
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