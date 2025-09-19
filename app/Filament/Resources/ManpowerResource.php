<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ManpowerResource\Pages;
use App\Filament\Resources\ManpowerResource\RelationManagers;
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
use Filament\Forms\Set;

class ManpowerResource extends Resource
{
    public static function getBreadcrumb(): string
    {
        return 'Man Power';
    }

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'Man power';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'man_power');
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
        \Log::info('ManpowerResource Query - Project ID: ' . ($projectId ?? 'null'));

        // Only filter if a specific project is selected (not "All Projects")
        if ($projectId && $projectId !== '' && $projectId !== null) {
            $query->whereHas('projects', function ($projectQuery) use ($projectId) {
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
                TextInput::make('email')->email()->required(),
                TextInput::make('nip')->label('NIP')->required(),

                // These fields will be stored in JSON format in description column
                TextInput::make('group')
                    ->label('Jabatan')
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        self::updateDescription($set, $get, 'group', $state);
                    }),

                TextInput::make('notelp')
                    ->label('No. Telepon')
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        self::updateDescription($set, $get, 'notelp', $state);
                    }),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-Laki',
                        'P' => 'Perempuan',
                    ])
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        self::updateDescription($set, $get, 'gender', $state);
                    }),

                Select::make('project_id')
                    ->label('Proyek')
                    ->options(Project::all()->pluck('name', 'id'))
                    ->default(fn() => session('selected_project_id') ?? null)
                    ->disabled(fn() => filled(session('selected_project_id')))
                    ->dehydrated()
                    ->live(),

                Select::make('placement_id')
                    ->label('Placement')
                    ->options(function (Get $get) {
                        $projectId = $get('project_id') ?? ($GLOBALS['project_global'] ?? null);

                        if (!$projectId || $projectId === '') {
                            return [];
                        }

                        return Placement::whereHas('project', function ($query) use ($projectId) {
                            $query->where('project_id', $projectId);
                        })
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->default(function () {
                        $projectId = session('selected_project_id') ?? null;
                        if ($projectId && $projectId !== '') {
                            $firstPlacement = Placement::whereHas('project', function ($query) use ($projectId) {
                                $query->where('project_id', $projectId);
                            })->first();

                            return $firstPlacement?->id;
                        }
                        return null;
                    })
                    ->disabled(fn(Get $get): bool => !$get('project_id'))
                    ->dehydrated()
                    ->required(),

                // Hidden field to store the JSON description
                Forms\Components\Hidden::make('description'),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn($context) => $context === 'create'),
            ])
            ->mutateFormDataBeforeFill(function (array $data): array {
                // When editing, populate the form fields from JSON description
                if (isset($data['description'])) {
                    $description = json_decode($data['description'], true) ?? [];
                    $data['group'] = $description['group'] ?? '';
                    $data['notelp'] = $description['notelp'] ?? '';
                    $data['gender'] = $description['gender'] ?? '';
                }
                return $data;
            })
            ->mutateFormDataBeforeSave(function (array $data): array {
                // Before saving, create the JSON description
                $description = [
                    'group' => $data['group'] ?? '',
                    'notelp' => $data['notelp'] ?? '',
                    'gender' => $data['gender'] ?? '',
                ];
                
                $data['description'] = json_encode($description);
                
                // Remove the individual fields as they're not database columns
                unset($data['group'], $data['notelp'], $data['gender']);
                
                return $data;
            });
    }

    /**
     * Helper method to update description JSON
     */
    private static function updateDescription(Set $set, Get $get, string $field, $value): void
    {
        $currentDescription = $get('description');
        $description = $currentDescription ? json_decode($currentDescription, true) : [];
        
        $description[$field] = $value;
        
        $set('description', json_encode($description));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('nip')->label('NIP')->searchable(),
                TextColumn::make('projects.name')->searchable()
                    ->label('Projects')
                    ->badge()
                    ->separator(', '),
                TextColumn::make('group')
                    ->label('Group')
                    ->getStateUsing(fn($record) => json_decode($record->description, true)['group'] ?? 'N/A')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('description', 'LIKE', '%"group":"' . $search . '"%');
                    }),

                TextColumn::make('notelp')
                    ->label('Phone')
                    ->getStateUsing(fn($record) => json_decode($record->description, true)['notelp'] ?? 'N/A')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('description', 'LIKE', '%"notelp":"' . $search . '"%');
                    }),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->getStateUsing(function ($record) {
                        $gender = json_decode($record->description, true)['gender'] ?? null;

                        return match ($gender) {
                            'L' => 'Laki-Laki',
                            'P' => 'Perempuan',
                            default => 'N/A',
                        };
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Search for both the key and display value
                        return $query->where(function ($q) use ($search) {
                            $q->where('description', 'LIKE', '%"gender":"L"%')
                                ->where(function ($subQ) use ($search) {
                                    $subQ->where('description', 'LIKE', '%' . strtolower($search) . '%')
                                        ->orWhere('description', 'LIKE', '%' . ucfirst(strtolower($search)) . '%');
                                })
                                ->when(stripos($search, 'laki') !== false || stripos($search, 'L') !== false, function ($subQ) {
                                    $subQ->orWhere('description', 'LIKE', '%"gender":"L"%');
                                })
                                ->when(stripos($search, 'perempuan') !== false || stripos($search, 'P') !== false, function ($subQ) {
                                    $subQ->orWhere('description', 'LIKE', '%"gender":"P"%');
                                });
                        });
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => url('/admin/manpowers/' . $record->id . '/edit')),
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