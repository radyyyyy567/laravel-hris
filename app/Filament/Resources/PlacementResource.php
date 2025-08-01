<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlacementResource\Pages;
use App\Filament\Resources\PlacementResource\RelationManagers;
use App\Models\Placement;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlacementResource extends Resource
{
    protected static ?string $model = Placement::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(225)->columnSpanFull(),
                Forms\Components\TimePicker::make('checkin_time'),
                Forms\Components\TimePicker::make('checkout_time'),
                Forms\Components\Textarea::make(name: 'description')->label('Keterangan')
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('radius')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255)
                    ->default(null),
                 Map::make('input_long_lat')
                    ->label('Lokasi')
                    ->default(['lat' => -6.2088, 'lng' => 106.8456]) // Set default value
                    ->defaultLocation(latitude: -6.2088, longitude: 106.8456)
                    ->showMarker(true)
                    ->clickable(true)
                    ->zoom(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('checkin_time'),
                Tables\Columns\TextColumn::make('checkout_time'),
                Tables\Columns\TextColumn::make('long_lat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('radius')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state === 'ready' ? 'success' : 'warning')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->searchable(),
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
            'index' => Pages\ListPlacements::route('/'),
            'create' => Pages\CreatePlacement::route('/create'),
            'edit' => Pages\EditPlacement::route('/{record}/edit'),
        ];
    }
}
