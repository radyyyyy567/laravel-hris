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
use Illuminate\Support\Facades\Request;


class PlacementResource extends Resource
{
    protected static ?string $model = Placement::class;

    protected static bool $shouldRegisterNavigation = false;

    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Get project ID from multiple sources in order of priority
        $projectId = null;

        // 1. Check global variable
        if (isset($GLOBALS['project_global']) && $GLOBALS['project_global'] !== '') {
            $projectId = $GLOBALS['project_global'];
        }
        // 2. Check session as fallback
        elseif (session()->has('selected_project_id') && session('selected_project_id') !== null) {
            $projectId = session('selected_project_id');
            // Also set global variable for consistency
            $GLOBALS['project_global'] = $projectId;
        }
        // 3. Check URL parameter as last resort
        elseif (request()->has('project') && request()->get('project') !== '') {
            $projectId = request()->get('project');
            // Also set global variable and session for consistency
            $GLOBALS['project_global'] = $projectId;
            session(['selected_project_id' => $projectId]);
        }

        // Debug: Log the project ID being used (remove this in production)
        \Log::info('PlacementResource Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
        if ($projectId && $projectId !== '' && $projectId !== null) {
            $query->whereHas('project.project', function ($projectQuery) use ($projectId) {
                $projectQuery->where('relation_placement_projects.project_id', $projectId);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(225)
                    ->columnSpanFull(),
                TextInput::make('kode_placement')->label('Kode Penempatan'),
                Select::make('status')
                    ->label('Pilih Status')
                    ->options([
                        '' => 'Pilih Status',
                        'office_hour' => 'Office Hour',

                        'custom' => 'Custom',
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

                Select::make('project_id')
                    ->label('Proyek')
                    ->options(Project::all()->pluck('name', 'id'))
                    ->default(fn() => session('selected_project_id') ?? null)
                    ->disabled(fn() => filled(session('selected_project_id')))
                    ->dehydrated()
                    ->live(),
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
                    Tables\Columns\TextColumn::make('kode_placement'),
                    Tables\Columns\TextColumn::make('project.project.name')
                    ->label('Projects')
                    ->badge(),
                    
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