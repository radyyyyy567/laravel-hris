<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManpowers extends ListRecords
{
    protected static string $resource = ManpowerResource::class;

    public function getTitle(): string
    {
        return 'Man Power'; // 👈 custom page title
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Man Power'),
        ];
    }
}
