<?php

namespace App\Filament\Resources\SpvResource\Pages;

use App\Filament\Resources\SpvResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Request;

class EditSpv extends EditRecord
{
    protected static string $resource = SpvResource::class;

    // ✅ Declare both properties here
    public int|string|null $projectId = null;
    

    public int|string|null $placementId = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing([ 'projects', 'placement', 'placementuser', 'pic']);

        // ✅ Initialize both properties
        $this->projectId = $GLOBALS['project_global'];
        
        
        $this->placementId = $this->record->placementuser?->placement_id;
         // Extract custom fields
        

        $data['gender'] = json_decode($this->record->description)?->gender;
        $data['group'] = json_decode($this->record->description)?->group;
        $data['notelp'] = json_decode($this->record->description)?->notelp;
        $data['project_id'] = $this->projectId;
        $data['placement_id'] = $this->placementId;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        
        $this->projectId = $data['project_id'];
        $this->placementId = $data['placement_id'];

        $this->customFields = [
            'group' => $data['group'] ?? null,
            'notelp' => $data['notelp'] ?? null,
            'gender' => $data['gender'] ?? null,
        ];

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

     $data['description'] = json_encode($this->customFields);
        
          unset(
            $data['group'],
            $data['notelp'],
            $data['gender'],
            $data['project_id'],
            $data['placement_id'],
        );
        return $data;
    }

    protected function afterSave(): void
    {
        
          if ($this->record->pic()->exists()) {
    $this->record->pic()->delete();
}
        $this->record->pic()->create([
            'project_id' => $this->projectId
        ]);
        
        // Check if the relationship exists and has records
if ($this->record->placementuser()->exists()) {
    $this->record->placementuser()->delete();
}
        
        $this->record->placementuser()->create([
            'placement_id' => $this->placementId
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->after(function () {
                    return redirect('/admin/spvs?project=' . $this->projectId);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return SpvResource::getUrl(name: 'index');
    }
}