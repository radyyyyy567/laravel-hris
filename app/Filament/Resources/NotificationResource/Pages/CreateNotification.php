<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNotification extends CreateRecord
{

     protected function mutateFormDataBeforeCreate(array $data): array
    {

        $this->user_id = $data['user_id'];
        
        unset($data['user_id']);

        return $data;
    }

    protected function afterCreate(): void
    {

        $this->record->user()->create([
            'user_id' => $this->user_id,
        ]);

    }



    protected static string $resource = NotificationResource::class;
}
