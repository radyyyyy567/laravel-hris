<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleAbsenceResource\Pages;
use App\Filament\Resources\ScheduleAbsenceResource\RelationManagers;
use App\Models\ScheduleAbsence;
use App\Models\User;
use Carbon\Carbon;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Request;


class ScheduleAbsenceResource extends Resource
{
    protected static ?string $model = ScheduleAbsence::class;
    protected static bool $shouldRegisterNavigation = false;

    public static function getBreadcrumb(): string
    {
        return 'Jadwal Absensi';
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (request()->filled('project')) {
            $projectId = request()->get('project');

            $query->whereHas('manpower.user.projects', function ($q) use ($projectId) {
                $q->where('projects.id', $projectId);
            });
        }

        return $query;
    }

    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Select::make('manpower_user_id')
    ->label('Pilih Man Power')
    ->options(fn (callable $get) => 
        User::role('man_power')
            ->whereHas('manpower', function ($query) use ($get) {
                $query->where('project_id', (int) $get('project'));
            })
            ->pluck('name', 'id')
    )
    ->searchable()
    ->required(),

TextInput::make('project') // hidden field
    ->default(fn () => request()->get('project'))
    ->dehydrated()
    ->hidden(),
                DatePicker::make('absence_date')
                    ->label('Tanggal Absensi')
                    ->required(),
                TimePicker::make('checkin_time')
                    ->label('Jam Masuk')
                    ->required(),
                TimePicker::make('checkout_time')
                    ->label('Jam Keluar')
                    ->required(),
                TextInput::make('description')
                    ->label('Keterangan')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'dinas_luar' => 'Perjalanan Dinas',
                        'in_office' => 'Work in Office',
                    ])
                    ->required(),
                TextInput::make('radius')
                    ->label('Radius (meter)')
                    ->numeric()
                    ->required(),
                Map::make('input_long_lat')
                    ->label('Lokasi')
                    ->default(['lat' => -6.2088, 'lng' => 106.8456]) // Set default value
                    ->defaultLocation(latitude: -6.2088, longitude: 106.8456)
                    ->showMarker(true)
                    ->clickable(true)
                    ->zoom(12)

                // ->defaultLocation(-6.2088, 106.8456) // Jakarta coordinates
                // ->zoom(12)
                // ->height('400px')
                // ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        // dd(ScheduleAbsence::with([
        //     'manpower.user',    // Manpower and related user
        //     'absence',          // Absence relationship
        //     // Add other relationships here if needed
        // ])->get()->toArray());
        return $table

            ->columns([
                TextColumn::make(name: 'manpower.user.name')->label('Man Power'),
                TextColumn::make(name: 'manpower.user.nip')->label('NIP'),
                TextColumn::make(name: 'name')->label('Di Buat'),
                TextColumn::make('absence_date')
                    ->label('Tanggal Absensi')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)
                        ->locale('id') // Bahasa Indonesia
                        ->translatedFormat('l, d F Y H:i')),
                TextColumn::make(name: 'checkin_time')->label('jam Masuk'),
                TextColumn::make(name: 'checkout_time')->label('Jam Keluar'),
                TextColumn::make(name: 'long_lat')->label('Lokasi'),
                TextColumn::make(name: 'status')->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListScheduleAbsences::route('/'),
            'create' => Pages\CreateScheduleAbsence::route('/create'),
            'edit' => Pages\EditScheduleAbsence::route('/{record}/edit'),
        ];
    }
}
