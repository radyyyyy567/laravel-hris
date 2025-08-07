<?php

namespace App\Filament\Resources\ManpowerResource\Pages;

use App\Filament\Resources\ManpowerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Request;

class EditManpower extends EditRecord
{
    protected static string $resource = ManpowerResource::class;

    // ✅ Declare both properties here
    public int|string|null $projectId = null;
    public int|string|null $groupId = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing(['group']);

        // ✅ Initialize both properties
        $this->projectId = Request::get('project');
        $this->groupId = $this->record->group?->group_id;

        $data['group_id'] = $this->groupId;
        $data['project'] = $this->projectId;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->groupId = $data['group_id'];

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        unset($data['group_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->group()->delete();
        $this->record->group()->create([
            'group_id' => $this->groupId,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->after(function () {
                    return redirect('/admin/manpowers?project=' . $this->projectId);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ManpowerResource::getUrl(name: 'index', parameters: ['project' => $this->projectId]);
    }
}