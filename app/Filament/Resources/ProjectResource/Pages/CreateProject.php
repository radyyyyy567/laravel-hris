<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $this->picUserId = $data['pic_user_id'] ?? auth()->id();
        $this->manPowerIds = $data['manpower_user_ids'];
        $this->placementId = $data['placement_id'] ?? null;


        unset($data['pic_user_id']);
        unset($data['manpower_user_ids']);
        unset($data['placement_id']);

        return $data;
    }

    protected function afterCreate(): void
    {

        $this->record->pic()->create([
            'user_id' => $this->picUserId,
        ]);

        foreach ($this->manPowerIds as $userId) {
            $this->record->manpower()->create([
                'user_id' => $userId,
            ]);
        };

        

        $this->record->placement()->create([
            'placement_id' => $this->placementId,
        ]);

        if ($this->placementId) {
            $placement = \App\Models\Placement::find($this->placementId);
            if ($placement) {
                $placement->status = 'filled';
                $placement->save();
            }
        }


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
