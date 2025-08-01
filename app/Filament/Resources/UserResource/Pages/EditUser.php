<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']); // don't update it if left blank
        } else {
            $data['password'] = bcrypt($data['password']); // hash if filled
        }
    
        return $data;
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->check() && auth()->user()->can('update_user');
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
