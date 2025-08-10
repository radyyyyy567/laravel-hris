<?php

namespace App\Filament\Resources\RecapAbsenceResource\Pages;

use App\Filament\Resources\RecapAbsenceResource;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;

use Filament\Actions;

use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListRecapAbsences extends ListRecords
{
    protected static string $resource = RecapAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
             ExportAction::make()
                ->exports([
                    ExcelExport::make()->withColumns([
                        
                        Column::make('manpower.user.name')->heading('Nama User'),
                        Column::make('manpower.user.nip')->heading('NIP'),
                        Column::make('absence_date')->heading('Tanggal Attendance'),
                        Column::make('checkin_time')->heading('Jam Masuk'),
                        Column::make('checkout_time')->heading('Jam Keluar'),
                        Column::make('long_lat')->heading('Lokasi'),
                        
                        Column::make('status')->heading('Status'),


                        Column::make('created_at')->heading('Creation date'),
                    ]),
                ]),
            Actions\CreateAction::make(),
        ];
    }
}
