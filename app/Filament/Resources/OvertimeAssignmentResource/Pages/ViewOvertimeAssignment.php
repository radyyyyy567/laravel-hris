<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

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
