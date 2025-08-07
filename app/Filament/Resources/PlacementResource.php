<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlacementResource\Pages;
use App\Filament\Resources\PlacementResource\RelationManagers;
use App\Models\Placement;
use App\Models\Project;
use App\Models\User;
use Auth;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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
                    ->maxLength(225)
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Pilih Status')
                    ->options([
                        '' => 'Pilih Status',
                        'office_hour' => 'Office Hour',
                        'ready' => 'Ready',
                        'busy' => 'Busy',
                        'on_leave' => 'On Leave',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state === 'office_hour') {
                            $set('checkin_time', '08:00');
                            $set('checkout_time', '17:00');
                        } else {
                            // Clear the times when other statuses are selected
                            $set('checkin_time', null);
                            $set('checkout_time', null);
                        }
                    })
                    ->searchable()
                    ->required(),
                TimePicker::make('checkin_time')
                    ->label('Check In')
                    ->disabled(fn(callable $get) => $get('status') === 'office_hour')
                    ->dehydrated(fn(callable $get) => $get('status') === 'office_hour' || $get('checkin_time') !== null)
                    ->reactive(),

                TimePicker::make('checkout_time')
                    ->label('Check Out')
                    ->disabled(fn(callable $get) => $get('status') === 'office_hour')
                    ->dehydrated(fn(callable $get) => $get('status') === 'office_hour' || $get('checkout_time') !== null)
                    ->reactive(),
                
                Forms\Components\Select::make('project_id')
                    ->label('Pilih Project')
                    ->options(Project::pluck('name', 'id'))
                    ->searchable()
                    ->default(fn($record) => $record?->project_id)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('radius')
                    ->required()
                    ->numeric()
                    ->default(0),



                Map::make('input_long_lat')
                    ->label('Lokasi')
                    ->default(['lat' => -6.2088, 'lng' => 106.8456])
                    ->defaultLocation(latitude: -6.2088, longitude: 106.8456)
                    ->showMarker(true)
                    ->clickable(true)
                    ->zoom(12),
            ])->columns(1);
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