<?php

namespace App\Filament\Resources\AbsenceResource\Pages;

use App\Filament\Resources\AbsenceResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class ListAbsences extends ListRecords
{
    protected static string $resource = AbsenceResource::class;

public function getTitle(): string
    {
        return 'Attendance';
    }


    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),    
                   ExportAction::make()
                ->exports([
                    ExcelExport::make()->withColumns([

                        Column::make('manpower.user.name')->heading('Nama User'),
                        Column::make('manpower.user.nip')->heading('NIP'),
                        Column::make('absence_date')->heading('Tanggal Attendance')->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y')),
                        Column::make('checkin_time')->heading('Jam Masuk'),
                        Column::make('checkout_time')->heading('Jam Keluar'),
                        Column::make('long_lat')->heading('Lokasi'),
                        Column::make('description')->heading('Description'),
                        Column::make('status')->heading('Status'),


                        Column::make('created_at')->heading('Creation date'),
                    ]),
                ]),
        ];
    }
}
