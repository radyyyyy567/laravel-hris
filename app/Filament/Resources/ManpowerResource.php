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


class ManpowerResource extends Resource
{
    public static function getBreadcrumb(): string
    {
        return 'Man Power'; // 👈 replaces "Users" in breadcrumbs
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

        // Filter by project if project parameter exists in URL
        if (request()->has('project') && request()->get('project')) {
            $projectId = request()->get('project');
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
                TextInput::make('name'),
                TextInput::make('email'),
                TextInput::make('nip')->label('NIP'),
                Select::make('group_id')
                    ->label('Pilih Jabatan')
                    ->options(Group::pluck('name', 'id'))
                    ->default(fn($record) => $record?->group?->user_id)
                    ->searchable()
                    ->required(),

Select::make('project_id')
    ->label('Proyek')
    ->options(Project::all()->pluck('name', 'id'))
    ->default(Request::get('project'))
    ->disabled()
    ->dehydrated()
    ->live(), // Add live() to update placement options when project changes

Select::make('placement_id')
    ->label('Placement')
    ->options(function (Get $get) {
        // Get the selected project ID
        $projectId = $get('project_id');
        
        // If no project selected, return empty array
        if (!$projectId) {
            return [];
        }
        
        // Get placements for the selected project through the pivot table
        return Placement::whereHas('project', function($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->pluck('name', 'id')
            ->toArray();
    })
    ->default(function () {
        // Get default placement ID from request if available
        return Request::get('placement_id');
    })
    ->disabled(fn (Get $get): bool => !$get('project_id')) // Disabled if no project selected
    ->dehydrated(),
                TextInput::make('password')
                    ->password()
                    ->revealable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('projects.name')
                    ->label('Projects')
                    ->badge()
                    ->separator(', '),
                TextColumn::make(name: 'group.group.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => url('/admin/manpowers/' . $record->id . '/edit?project=' . Request::get('project'))),

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
}