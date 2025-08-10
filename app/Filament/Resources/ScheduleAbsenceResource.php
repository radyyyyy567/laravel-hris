<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleAbsenceResource\Pages;
use App\Filament\Resources\ScheduleAbsenceResource\RelationManagers;
use App\Models\ScheduleAbsence;
use App\Models\User;
use App\Models\Manpower;
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
        return 'Jadwal Attendance';
    }

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
        \Log::info('ScheduleAbsenceResource Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
        if ($projectId && $projectId !== '' && $projectId !== null) {
            $query->whereHas('manpower.user.projects', function ($projectQuery) use ($projectId) {
                $projectQuery->where('projects.id', $projectId);
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
                Select::make('manpower_id')
                    ->label('Pilih Man Power')
                    ->options(function (callable $get) {
                        $projectId = $get('project') ?? request()->get('project');
                        
                        if (!$projectId) {
                            return [];
                        }

                        return Manpower::whereHas('user.projects', function ($query) use ($projectId) {
                                $query->where('projects.id', $projectId);
                            })
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive(),

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
                        'wfh' => 'Work From Home',
                        'sick' => 'Sakit',
                        'permission' => 'Izin',
                        'holiday' => 'Libur',
                    ])
                    ->required(),

                TextInput::make('radius')
                    ->label('Radius (meter)')
                    ->numeric()
                    ->default(100)
                    ->required(),

                Map::make('input_long_lat')
                    ->label('Lokasi')
                    ->default(['lat' => -6.2088, 'lng' => 106.8456]) // Jakarta default
                    ->defaultLocation(latitude: -6.2088, longitude: 106.8456)
                    ->showMarker(true)
                    ->clickable(true)
                    ->zoom(12)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('manpower.user.name')
                    ->label('Man Power')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('manpower.user.nip')
                    ->label('NIP')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('absence_date')
                    ->label('Tanggal Absensi')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)
                        ->locale('id') // Bahasa Indonesia
                        ->translatedFormat('l, d F Y'))
                    ->sortable(),

                TextColumn::make('checkin_time')
                    ->label('Jam Masuk')
                    ->time()
                    ->sortable(),

                TextColumn::make('checkout_time')
                    ->label('Jam Keluar')
                    ->time()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_office' => 'success',
                        'dinas_luar' => 'info',
                        'wfh' => 'warning',
                        'sick' => 'danger',
                        'permission' => 'gray',
                        'holiday' => 'secondary',
                        default => 'gray',
                    }),

                TextColumn::make('long_lat')
                    ->label('Lokasi')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('radius')
                    ->label('Radius (m)')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'dinas_luar' => 'Perjalanan Dinas',
                        'in_office' => 'Work in Office',
                        'wfh' => 'Work From Home',
                        'sick' => 'Sakit',
                        'permission' => 'Izin',
                        'holiday' => 'Libur',
                    ]),

                Tables\Filters\Filter::make('absence_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('absence_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('absence_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('absence_date', 'desc');
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