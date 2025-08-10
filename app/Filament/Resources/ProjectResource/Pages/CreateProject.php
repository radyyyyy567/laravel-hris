<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    
    protected function mutateFormDataBeforeCreate(array $data): array {
        $data['status'] = 'Work';
        return $data;
    }


    public static function canAccess(array $parameters = []): bool
    {
        return auth()->check() && auth()->user()->can('create_project');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}
