<?php
namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Mount the page and validate the project exists
     */
    public function mount(int|string $record): void
    {
        try {
            // Check if the project exists and user has access
            $project = static::getResource()::resolveRecordRouteBinding($record);
            
            if (!$project) {
                // Project not found, redirect to projects index with error
                session()->flash('error', 'Project not found.');
                redirect()->to(static::getResource()::getUrl('index'));
                return;
            }

            // Call parent mount to set up the record
            parent::mount($record);
            
        } catch (ModelNotFoundException $e) {
            // Handle case where project doesn't exist
            session()->flash('error', 'The selected project could not be found.');
            redirect()->to(static::getResource()::getUrl('index'));
            return;
        } catch (\Exception $e) {
            // Handle any other errors
            session()->flash('error', 'An error occurred while loading the project.');
            redirect()->to(static::getResource()::getUrl('index'));
            return;
        }
    }

    /**
     * Handle cases where the record parameter is invalid
     */
    protected function handleRecordNotFound(): never
    {
        session()->flash('error', 'Project not found or you do not have permission to edit it.');
        redirect()->to(static::getResource()::getUrl('index'));
    }
}