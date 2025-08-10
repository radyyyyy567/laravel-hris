<?php

namespace App\Filament\Resources;

use App\Models\Absence;
use App\Filament\Resources\RecapAbsenceResource\Pages;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecapAbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Absensi';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $label = 'Rekap Absensi';
    protected static ?string $pluralLabel = 'Rekap Absensi';

    public static function getBreadcrumb(): string
    {
        return 'Rekap Absensi';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Created By')
                ->disabled(),
            Forms\Components\DatePicker::make('absence_date')
                ->label('Tanggal Absensi')
                ->disabled(),
            Forms\Components\TimePicker::make('checkin_time')
                ->label('Jam Masuk')
                ->disabled(),
            Forms\Components\TimePicker::make('checkout_time')
                ->label('Jam Keluar')
                ->disabled(),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'present' => 'Present',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpha' => 'Alpha',
                    'dinas_luar' => 'Dinas Luar',
                ])
                ->disabled(),
            Forms\Components\Textarea::make('description')
                ->label('Keterangan')
                ->disabled(),
            Forms\Components\TextInput::make('long_lat')
                ->label('Lokasi')
                ->disabled(),
        ]);
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
        \Log::info('RecapAbsenceResource Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
        if ($projectId && $projectId !== '' && $projectId !== null) {
            $query->whereHas('manpower.user.projects', function ($projectQuery) use ($projectId) {
                $projectQuery->where('projects.id', $projectId);
            });
        }

        return $query;
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
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('checkin_time')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('checkout_time')
                    ->label('Jam Keluar')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'info',
                        'alpha' => 'danger',
                        'dinas_luar' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                        'dinas_luar' => 'Dinas Luar',
                        default => ucfirst($state),
                    }),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('long_lat')
                    ->label('Lokasi')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('absence_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('absence_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date']) {
                            $indicators['start_date'] = 'Dari: ' . Carbon::parse($data['start_date'])->format('d/m/Y');
                        }
                        if ($data['end_date']) {
                            $indicators['end_date'] = 'Sampai: ' . Carbon::parse($data['end_date'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                SelectFilter::make('status')
                    ->options([
                        'present' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                        'dinas_luar' => 'Dinas Luar',
                    ])
                    ->label('Status Kehadiran')
                    ->multiple(),

                SelectFilter::make('manpower_user')
                    ->label('Karyawan')
                    ->options(function () {
                        $projectId = null;
                        
                        // Get project ID from global/session/request
                        if (isset($GLOBALS['project_global']) && $GLOBALS['project_global'] !== '') {
                            $projectId = $GLOBALS['project_global'];
                        } elseif (session()->has('selected_project_id') && session('selected_project_id') !== null) {
                            $projectId = session('selected_project_id');
                        } elseif (request()->has('project') && request()->get('project') !== '') {
                            $projectId = request()->get('project');
                        }

                        $query = User::role('man_power');
                        
                        // Filter by project if specified
                        if ($projectId && $projectId !== '' && $projectId !== null) {
                            $query->whereHas('projects', function ($q) use ($projectId) {
                                $q->where('projects.id', $projectId);
                            });
                        }
                        
                        return $query->get()
                            ->mapWithKeys(function ($user) {
                                return [$user->id => "{$user->name} ({$user->nip})"];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('manpower.user', function ($q) use ($data) {
                                $q->whereIn('users.id', $data['values']);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Excel')
                        ->color('success'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('absence_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecapAbsences::route('/'),
            
        ];
    }
}