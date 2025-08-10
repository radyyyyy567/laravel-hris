<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Request;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return 'Data Proyek';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Proyek')->icon('heroicon-o-plus')
                
        ];
    }
}
