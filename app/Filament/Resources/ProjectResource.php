<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Placement;
use App\Models\Project;
use App\Models\User;
use Faker\Provider\Image;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
     public static function getBreadcrumb(): string
    {
        return 'Data Proyek';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_project');
    }



    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('pic')) {
            return $query->whereHas('pic', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        return $query;
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name'),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('project_logos')
                    ->nullable(),
                RichEditor::make('description')->columnSpanFull(),
                TextInput::make('status'),
                Auth::user()->hasRole('super_admin')
                ? Select::make('pic_user_id')
                    ->label('Pilih PIC')
                    ->options(User::role('pic')->pluck('name', 'id'))
                    ->default(fn($record) => $record?->pic?->user_id)
                    ->searchable()
                    ->required()
                : Hidden::make('pic_user_id')
                    ->default(fn() => auth()->id()),
                Select::make('placement_id')
                    ->label('Pilih Penempatan')
                    ->options(Placement::where('status', 'ready')->pluck('name', 'id'))
                    ->default(fn($record) => $record?->placement_id)
                    ->searchable()
                    ->required(),
                MultiSelect::make('manpower_user_ids')
                    ->label('Pilih Man Power')
                    ->options(User::role('man_power')->pluck('name', 'id'))
                    ->default(fn($record) => $record?->manpower?->pluck('user_id')->toArray() ?? [])
                    ->searchable()
                    ->required(),
            ]);


    }

    public static function table(Table $table): Table
    {

        return $table

            ->columns([
                //
                TextColumn::make('name'),
                TextColumn::make('description')->html(),
                TextColumn::make('status'),
                ImageColumn::make('logo')->label('Logo'),
                TextColumn::make('pic.user.name')->label('PIC'),
                TextColumn::make('manpower.user.name')->label('Man Power'),

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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
