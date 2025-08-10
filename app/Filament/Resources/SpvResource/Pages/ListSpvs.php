<?php

namespace App\Filament\Resources\SpvResource\Pages;


use App\Filament\Resources\SpvResource;

use App\Imports\SpvImport;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Request;
use App\Imports\ScheduleAbsenceImport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Components\Actions\Action;

class ListSpvs extends ListRecords
{
    protected static string $resource = SpvResource::class;

    public function getTitle(): string
    {
        return 'Spv'; // 👈 custom page title
    }

    protected function getHeaderActions(): array
    {
        return [
          
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->use(SpvImport::class)
                ->sampleExcel(
                    sampleData: [
                        [
                            'nama' => 'Katrina',
                            'nip' => '12345678',
                            'email' => 'katrina@example.com',
                            'profesi' => 'Operator',
                            'no_telp' => '081234567890',
                            'jenis_kelamin' => 'P', // 'L' = Laki-Laki, 'P' = Perempuan
                            'password' => '12345678',
                            'kode_placement' => 'HJSDA',
                            
                        ],
                        [
                            'nama' => 'Yusuf',
                            'nip' => '87654321',
                            'email' => 'yusuf@example.com',
                            'profesi' => 'Teknisi',
                            'no_telp' => '089876543210',
                            'password' => '12345678',
                            'jenis_kelamin' => 'L',
                            'kode_placement' => 'HJSDA',
                            
                        ],
                    ],
                    fileName: 'spv-sample.xlsx',
                    sampleButtonLabel: 'Download Sample',
                    customiseActionUsing: fn(Action $action) => $action
                        ->color('secondary')
                        ->icon('heroicon-m-user-group')
                        ->requiresConfirmation(),
                ),
            CreateAction::make()
                ->label(label: 'Tambah Spv')
                ->url(fn() => url('/admin/spvs/create'))
        ];
    }
}
