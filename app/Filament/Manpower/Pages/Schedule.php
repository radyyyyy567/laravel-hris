<?php

namespace App\Filament\Manpower\Pages;

use Filament\Pages\Page;

class Schedule extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

public function getTitle(): string
    {
        return '';
    }

    protected static string $view = 'filament.manpower.pages.schedule';
}
