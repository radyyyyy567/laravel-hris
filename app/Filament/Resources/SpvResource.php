<?php
namespace App\Filament\Resources;
use App\Filament\Resources\SpvResource\Pages;
use App\Filament\Resources\SpvResource\RelationManagers;
use App\Models\Project;

use Spatie\Permission\Models\Role;

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
use Illuminate\Support\Facades\Request;
use PhpParser\Node\Stmt\Label;
use App\Models\Group;
use App\Models\Placement;
use Filament\Forms\Get;

class SpvResource extends Resource
{
    public static function getBreadcrumb(): string
    {
        return 'Spv'; // 👈 replaces "Users" in breadcrumbs
    }

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'Spv';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'spv');
        });

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
        \Log::info('SpvResource Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
        if ($projectId && $projectId !== '' && $projectId !== null) {
            $query->whereHas('pic.project', function ($projectQuery) use ($projectId) {
                $projectQuery->where('project_pics.project_id', $projectId);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('email'),
                TextInput::make('nip')->label('NIP'),

                TextInput::make('group')->label('Jabatan'),
                TextInput::make('notelp')->label('No. Telepon'),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        '' => 'Pilih Jenis Kelamin',
                        'L' => 'Laki-Laki',
                        'P' => 'Perempuan',
                    ]),
                Select::make('project_id')
                    ->label('Proyek')
                    ->options(Project::all()->pluck('name', 'id'))
                    ->default(fn() => session('selected_project_id') ?? null)
                    ->disabled(fn() => filled(session('selected_project_id')))
                    ->dehydrated()
                    ->live(),// Add live() to update placement options when project changes

                Select::make('placement_id')
                    ->label('Placement')
                    ->options(function (Get $get) {
                        // Get the selected project ID from form or global variable
                        $projectId = $get('project_id') ?? ($GLOBALS['project_global'] ?? null);

                        // If no project selected, return empty array
                        if (!$projectId || $projectId === '') {
                            return [];
                        }

                        // Get placements for the selected project through the pivot table
                        return Placement::whereHas('project', function ($query) use ($projectId) {
                            $query->where('project_id', $projectId);
                        })
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->default(function () {
                        // You can set a default placement based on your business logic
                        // For example, get the first placement of the selected project
                        $projectId = session('selected_project_id') ?? null;
                        if ($projectId && $projectId !== '') {
                            $firstPlacement = Placement::whereHas('project', function ($query) use ($projectId) {
                                $query->where('project_id', $projectId);
                            })->first();

                            return $firstPlacement?->id;
                        }
                        return null;
                    })
                    ->disabled(fn(Get $get): bool => !$get('project_id')) // Disabled if no project selected
                    ->dehydrated()
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->revealable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('pic.project.name')->searchable()
                    ->label('Projects')
                    ->badge()
                    ->separator(', '),
                TextColumn::make('group')->searchable()
                    ->label('Group')
                    ->getStateUsing(fn($record) => json_decode($record->description, true)['group'] ?? 'N/A'),

                TextColumn::make('notelp')->searchable()
                    ->label('Phone')
                    ->getStateUsing(fn($record) => json_decode($record->description, true)['notelp'] ?? 'N/A'),

                TextColumn::make('gender')->searchable()
                    ->label('Gender')
                    ->getStateUsing(function ($record) {
                        $gender = json_decode($record->description, true)['gender'] ?? null;

                        return match ($gender) {
                            'L' => 'Laki-Laki',
                            'P' => 'Perempuan',
                            default => 'N/A',
                        };
                    }),

            ])
            ->filters([
                // You can add project filter here if needed
                // But since project selection is handled globally, you might not need it
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    // Remove project parameter from URL since we're using global variable
                    ->url(fn($record) => url('/admin/spvs/' . $record->id . '/edit')),
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
            'index' => Pages\ListSpvs::route('/'),
            'create' => Pages\CreateSpv::route('/create'),
            'edit' => Pages\EditSpv::route('/{record}/edit'),
        ];
    }

    /**
     * Helper method to get current project ID
     */
    public static function getCurrentProjectId(): ?int
    {
        return $GLOBALS['project_global'] ?? null;
    }

    /**
     * Helper method to check if "All Projects" is selected
     */
    public static function isAllProjectsSelected(): bool
    {
        return empty($GLOBALS['project_global']) || $GLOBALS['project_global'] === '';
    }
}