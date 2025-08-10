<?php

namespace App\Filament\Resources\PlacementResource\Pages;

use App\Filament\Resources\PlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;


class ListPlacements extends ListRecords
{
    protected static string $resource = PlacementResource::class;


    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label("Tambah Penempatan")->url(fn () => url('/admin/placements/create?project=' . Request::get('project')))       ,
        ];
    }
  protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
 
}
