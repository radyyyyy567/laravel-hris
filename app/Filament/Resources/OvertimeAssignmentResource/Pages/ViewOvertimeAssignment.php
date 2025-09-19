<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use App\Models\RelationNotificationUser;
use App\Models\ScheduleAbsence;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Notification as Notifications;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Carbon\Carbon;

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
                        if (isset($description->submission_type) && $description->submission_type !== 'overtime') {
                            // Query ScheduleAbsence through the manpower relationship
                            $records = ScheduleAbsence::with('manpower.user') // eager load manpower and user
                                ->whereDate('absence_date', '>=', $this->record->start_time)
                                ->whereDate('absence_date', '<=', $this->record->end_time)
                                ->whereHas('manpower.user', function ($query) {
                                $query->where('id', $this->record->projectuser->first()->user_id);})
                                ->update([
                                    'status' => $description->submission_type,
                                ]);
                        }
                    }

                    

                    $this->record->update(['status' => 'approved']);
        
                    
                    $notif = Notifications::create([
                        'title' => 'Submission Diterima',
                        'description' => 'Pengajuan '.json_decode($this->record->description)->submission_type.' anda pada tanggal '.\Carbon\Carbon::parse($this->record->submission_date)->format('d-m-Y').' telah disetujui',
                        'type' => 'submission'
                    ]);

                    RelationNotificationUser::create([
                       'user_id' => $this->record->projectuser->first()->user_id,
                       'notification_id' => $notif->id
                    ]);

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

                    $notif = Notifications::create([
                        'title' => 'Submission Ditolak',
                        'description' => 'Pengajuan '.json_decode($this->record->description)->submission_type.' anda pada tanggal '.\Carbon\Carbon::parse($this->record->submission_date)->format('d-m-Y').' telah ditolak',
                        'type' => 'submission'
                    ]);

                    RelationNotificationUser::create([
                       'user_id' => $this->record->projectuser->first()->user_id,
                       'notification_id' => $notif->id
                    ]);

                

                    Notification::make()
                        ->title('Ditolak')
                        ->success()
                        ->body('Lembur telah ditolak.')
                        ->send();
                }),
        ];
    }

   public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Detail Pengajuan')
                ->schema([
                    TextEntry::make('name')
                        ->label('Name'),

                    TextEntry::make('submission_date')
                        ->label('Submission date')
                        ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d-m-Y')),

                    TextEntry::make('start_time')
                        ->label('Mulai Pada'),

                    TextEntry::make('end_time')
                        ->label('Sampai Pada'),

                    TextEntry::make('submission_type')
                        ->label('Jenis Pengajuan')
                        ->getStateUsing(fn ($record) => json_decode($record->description, true)['submission_type'] ?? 'N/A'),
                    TextEntry::make('status')
    ->label('Status')
    ->badge()
     ->formatStateUsing(fn ($state) => ucfirst(strtolower($state)))
    ->color(fn ($state) => match (strtolower($state)) {
        'rejected' => 'danger', // red
        'approved' => 'success', // green
        default => 'gray',       // default color
    }),

                    ImageEntry::make('evidence')
                        ->label('Foto')
                        ->getStateUsing(function ($record) {
                            $evidence = json_decode($record->description, true)['evidence'] ?? null;
                            return $evidence ? asset('storage/' . $evidence) : null;
                        }),
                ]),
        ]);
}

}