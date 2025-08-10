<?php

namespace App\Filament\Resources\HoldSubmissionResource\Pages;

use App\Filament\Resources\HoldSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHoldSubmission extends EditRecord
{
    protected static string $resource = HoldSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
