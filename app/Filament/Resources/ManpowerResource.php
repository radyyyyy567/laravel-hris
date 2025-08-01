<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManpowerResource\Pages;
use App\Filament\Resources\ManpowerResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class ManpowerResource extends Resource
{

    public static function getBreadcrumb(): string
{
    return 'Man Power'; // 👈 replaces "Users" in breadcrumbs
}
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'Man power';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
             return parent::getEloquentQuery()->whereHas('roles', function ($query) {
        $query->where('name', 'man_power');
    });
    }

    public static function form(Form $form): Form
    {
         return $form
            ->schema([
                
                TextInput::make('name'),
                TextInput::make('email'),
                TextInput::make('nip')->label('NIP'),
                TextInput::make('password')
                    ->password()
                    ->revealable(),
                Select::make('roles')->relationship('roles', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('projects.name')
    ->label('Projects')
    ->badge()
    ->separator(', ')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManpowers::route('/'),
            'create' => Pages\CreateManpower::route('/create'),
            'edit' => Pages\EditManpower::route('/{record}/edit'),
        ];
    }
}
