<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeAssignmentResource\Pages;
use App\Filament\Resources\OvertimeAssignmentResource\RelationManagers;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use GrahamCampbell\ResultType\Success;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class OvertimeAssignmentResource extends Resource
{

    protected static ?string $model = OvertimeAssignment::class;
    protected static bool $shouldRegisterNavigation = false;
protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user->hasRole('man_power')) {
            // Only for 'man_power' (even if they also have 'pic')
            return $query->whereHas('projectuser.user', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($user->hasRole('pic')) {
            return $query->whereHas('projectuser.project.pic', function ($q) use ($user) {
                $q->where('user_id', $user->id); // <-- from project_pics table
            });
        }

        // Other roles (admin, etc.) can see everything
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
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord || $livewire instanceof \Filament\Resources\Pages\EditRecord),

Placeholder::make('project_name')
->label('Projek')
->content(fn ($record) => $record->projectuser->first()?->project?->name ?? '-')
->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\ViewRecord || $livewire instanceof \Filament\Resources\Pages\EditRecord)

                    
                
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
                TextColumn::make('name'),
                TextColumn::make('submission_date')->dateTime(),
                TextColumn::make('start_time')->dateTime(),
                TextColumn::make('end_time')->dateTime(),
                TextColumn::make('description'),
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
                Tables\Actions\ViewAction::make()
                ->url(fn ($record) => static::getUrl('view', ['record' => $record]))
        ->openUrlInNewTab(false),
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
            'index' => Pages\ListOvertimeAssignments::route('/'),
            'create' => Pages\CreateOvertimeAssignment::route('/create'),
            'edit' => Pages\EditOvertimeAssignment::route('/{record}/edit'),
            'view' => Pages\ViewOvertimeAssignment::route('/{record}')
        ];
    }
}
