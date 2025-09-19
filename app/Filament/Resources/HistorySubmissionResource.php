<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorySubmissionResource\Pages;
use App\Filament\Resources\HistorySubmissionResource\RelationManagers;
use App\Models\OvertimeAssignment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use GrahamCampbell\ResultType\Success;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class HistorySubmissionResource extends Resource
{

    protected static ?string $model = OvertimeAssignment::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getBreadcrumb(): string
    {
        return 'Pengajuan';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_overtime::assignment');
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_overtime::assignment');
    }

    public static function canUpdate(): bool
    {
        return auth()->user()?->can(abilities: 'update_overtime::assignment');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can(abilities: 'delete_overtime::assignment');
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('status', '!=', 'waiting');

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
        \Log::info('Submission Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
       if ($projectId && $projectId !== '' && $projectId !== null) {
     $query->where('status', '!=', 'waiting') // exclude waiting
          ->whereHas('projectuser.user.projects', function ($projectQuery) use ($projectId) {
              $projectQuery->where('projects.id', $projectId);
          });
}

        return $query;
    }


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                TextInput::make('name')->required(),
                DatePicker::make('submission_date')->required(),
                TimePicker::make('start_time')->required(),
                TimePicker::make('end_time')->required(),
                TextInput::make('description')->required(),
                Select::make('project_id')
                    ->label('Projek')
                    ->options(function () {
                        return \App\Models\Project::whereHas('manpower', function ($query) {
                            $query->where('user_id', auth()->id());
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord || $livewire instanceof \Filament\Resources\Pages\EditRecord),

                Placeholder::make(name: 'project_name')
                    ->label('Projek')
                    ->content(fn($record) => $record->projectuser->first()?->project?->name ?? '-')
                    ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\ViewRecord || $livewire instanceof \Filament\Resources\Pages\EditRecord)



                // Select::make('status')
                //     ->label('Status')
                //     ->options([
                //         'approved' => 'Approved',
                //         'rejected' => 'Rejected',
                //         'waiting' => 'Waiting',
                //     ])
                //     ->default('waiting') // optional
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                TextColumn::make('projectuser.user.name')->searchable(),
                TextColumn::make('submission_date')->dateTime(),
                TextColumn::make('start_time')->dateTime(),
                TextColumn::make('end_time')->dateTime(),
                TextColumn::make('submission_type')->searchable()
                    ->label('Tipe Pengajuan')
                    ->getStateUsing(fn($record) => json_decode($record->description, true)['submission_type'] ?? 'N/A'),
                ImageColumn::make('evidence')
                    ->label('Foto')
                    ->getStateUsing(function ($record) {
                        $evidence = json_decode($record->description, true)['evidence'] ?? null;
                        return $evidence ? asset('storage/' . $evidence) : null;
                    })
                    ->square()
                    ->size(50),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'waiting' => 'warning',
                        default => 'gray',
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
               Tables\Actions\Action::make('view')
    ->label('View')
    ->icon('heroicon-s-eye') // 👁 Eye icon
    ->color('gray-700') // same color as default ViewAction
    ->url(fn ($record) => "/admin/overtime-assignments/{$record->id}")
    ->openUrlInNewTab(false)
,
                

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    
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
            'index' => Pages\ListHistorySubmissions::route('/'),
            'create' => Pages\CreateHistorySubmission::route('/create'),
            'edit' => Pages\EditHistorySubmission::route('/{record}/edit'),
            
        ];
    }
}
