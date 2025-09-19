<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use App\Exports\SubmissionExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListOvertimeAssignments extends ListRecords
{
    protected static string $resource = OvertimeAssignmentResource::class;

    public function getTitle(): string
    {
        return 'Pengajuan';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(
                        new SubmissionExport($this->getFilteredTableQuery()),
                        'pengajuan-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
                    );
                }),
            
            Actions\CreateAction::make()
                ->label('Buat Pengajuan')
                ->icon('heroicon-o-plus'),
        ];
    }
}