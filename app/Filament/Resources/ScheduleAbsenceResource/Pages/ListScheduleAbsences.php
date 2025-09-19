<?php

namespace App\Filament\Resources\ScheduleAbsenceResource\Pages;

use App\Filament\Resources\ScheduleAbsenceResource;
use Carbon\Carbon;
use App\Imports\ScheduleAbsenceImport;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Request;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Components\Actions\Action;

class ListScheduleAbsences extends ListRecords
{
    protected static string $resource = ScheduleAbsenceResource::class;


    public function getTitle(): string
    {
        return 'Jadwal Attendance';
    }

    protected function getHeaderActions(): array
    {
        return [
            // ✅ Use Pages\ExportAction instead of Tables\ExportAction
            ExportAction::make()
                ->exports([
                    ExcelExport::make()->withColumns([

                        Column::make('manpower.user.name')->heading('Nama User'),
                        Column::make('manpower.user.nip')->heading('NIP'),
                        Column::make('absence_date')->heading('Tanggal Attendance')->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y')),
                        Column::make('checkin_time')->heading('Jam Masuk'),
                        Column::make('checkout_time')->heading('Jam Keluar'),
                        Column::make('long_lat')->heading('Lokasi'),
                        Column::make('radius')->heading('Radius'),
                        Column::make('status')->heading('Status'),


                        Column::make('created_at')->heading('Creation date'),
                    ]),
                ]),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->use(ScheduleAbsenceImport::class)
                ->sampleExcel(
                    sampleData: [
                        [
                            'name' => 'Katrina',
                            'nip' => '1234',
                            'absence_date' => '2025-08-01 00:00:00',
                            'checkin_time' => '08:00:00',
                            'checkout_time' => '17:00:00',
                            'description' => 'Pekerja',
                            'radius' => 100,
                            'long_lat' => '123.456,-76.543',
                            'status' => 'biasa',
                        ],
                        [
                            'name' => 'Katrina',
                            'nip' => '1234',
                            'absence_date' => '2025-08-02 00:00:00',
                            'checkin_time' => '09:00:00',
                            'checkout_time' => '18:00:00',
                            'description' => 'Pekerja',
                            'radius' => 150,
                            'long_lat' => '124.321,-75.432',
                            'status' => 'dinas_luar',
                        ],
                    ],
                    fileName: 'schedule-absence-sample.xlsx',
                    sampleButtonLabel: 'Download Sample',
                    customiseActionUsing: fn(Action $action) => $action
                        ->color('secondary')
                        ->icon('heroicon-m-calendar')
                        ->requiresConfirmation(),
                ),
            CreateAction::make()
                ->label('Tambah Jadwal Attendance')
                ->url(fn() => url('/admin/schedule-absences/create?project=' . Request::get('project')))
        ];
    }

    // ✅ Move import action to table actions instead
    protected function getTableActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->sampleExcel(
                    sampleData: [
                        ['name' => 'John Doe', 'email' => 'john@doe.com', 'phone' => '123456789'],
                        ['name' => 'Jane Doe', 'email' => 'jane@doe.com', 'phone' => '987654321'],
                    ],
                    fileName: 'sample.xlsx',
                    exportClass: \App\Exports\SampleExport::class,
                    sampleButtonLabel: 'Download Sample',
                    customiseActionUsing: fn(Actions\Action $action) => $action
                        ->color('secondary')
                        ->icon('heroicon-m-clipboard')
                        ->requiresConfirmation(),
                ),
        ];
    }
}
