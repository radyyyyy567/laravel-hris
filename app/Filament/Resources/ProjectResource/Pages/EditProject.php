<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;


    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Make sure the relation is loaded

        $this->record->loadMissing(['pic', 'manpower']);

        // Even if null, assign to form so Select/MultiSelect binds correctly
        $data['pic_user_id'] = $this->record->pic?->user_id;

        $data['manpower_user_ids'] = $this->record->manpower->pluck('user_id')->toArray();

        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Safely assign, or null
        $this->picUserId = $data['pic_user_id'] ?? null;

        // Ensure it's always an array (or empty array)
        $this->manPowerIds = $data['manpower_user_ids'] ?? [];

        // Remove from form data so it's not saved directly to the model
        unset($data['pic_user_id'], $data['manpower_user_ids']);

        return $data;
    }



    protected function afterSave(): void
    {
        // ✅ Handle PIC (only update if changed)
        if ($this->picUserId) {
            $currentPic = $this->record->pic;

            if (!$currentPic || $currentPic->user_id !== $this->picUserId) {
                $currentPic?->delete();

                $this->record->pic()->create([
                    'user_id' => $this->picUserId,
                ]);
            }
        }

        // ✅ Sync manpower (add new, remove missing)
        if (is_array($this->manPowerIds)) {
            $existingUserIds = $this->record->manpower->pluck('user_id')->toArray();

            // 🔁 Remove unselected users
            $this->record->manpower()
                ->whereNotIn('user_id', $this->manPowerIds)
                ->delete();

            // ➕ Add newly selected users
            $newUserIds = array_diff($this->manPowerIds, $existingUserIds);
            foreach ($newUserIds as $userId) {
                $this->record->manpower()->create([
                    'user_id' => $userId,
                ]);
            }
        }
    }

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
