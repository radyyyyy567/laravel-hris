<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenceResource\Pages;
use App\Filament\Resources\AbsenceResource\RelationManagers;
use App\Models\Absence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationGroup = 'Absensi';

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
        \Log::info('AbsenceResource Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
        if ($projectId && $projectId !== '' && $projectId !== null) {
            $query->whereHas('manpower.user.projects', function ($projectQuery) use ($projectId) {
                $projectQuery->where('projects.id', $projectId);
            });
        }

        return $query;
    }

    public static function getBreadcrumb(): string
    {
        return 'Attendance';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Forms\Components\DatePicker::make('absence_date')
                    ->label('Absence Date')
                    ->required(),
                Forms\Components\TimePicker::make('checkin_time')
                    ->label('Check In Time'),
                Forms\Components\TimePicker::make('checkout_time')
                    ->label('Check Out Time'),
                Forms\Components\TextInput::make('long_lat')
                    ->label('Location (Lat, Long)'),
                Forms\Components\Textarea::make('description')
                    ->label('Description'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'sick' => 'Sick',
                        'permission' => 'Permission',
                    ])
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
                TextColumn::make('name')
                    ->label('Created By')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('absence_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('checkin_time')
                    ->label('Check In')
                    ->time()
                    ->sortable(),
                TextColumn::make('checkout_time')
                    ->label('Check Out')
                    ->time()
                    ->sortable(),
                TextColumn::make('long_lat')
                    ->label('Location')
                    ->limit(30),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'absent' => 'danger',
                        'sick' => 'info',
                        'permission' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'sick' => 'Sick',
                        'permission' => 'Permission',
                    ]),
                Tables\Filters\Filter::make('absence_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
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
            'index' => Pages\ListAbsences::route('/'),
            'create' => Pages\CreateAbsence::route('/create'),
            'edit' => Pages\EditAbsence::route('/{record}/edit'),
        ];
    }
}