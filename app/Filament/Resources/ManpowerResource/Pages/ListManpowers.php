<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Request;

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
            CreateAction::make()
    ->label('Tambah Man Power')
    ->url(fn () => url('/admin/manpowers/create?project=' . Request::get('project')))
        ];
    }
}
