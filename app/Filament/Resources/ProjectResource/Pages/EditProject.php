<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;


    




    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->check() && auth()->user()->can('update_project');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
