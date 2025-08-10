<?php

namespace App\Filament\Resources\HistorySubmissionResource\Pages;

use App\Filament\Resources\HistorySubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistorySubmission extends EditRecord
{
    protected static string $resource = HistorySubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
