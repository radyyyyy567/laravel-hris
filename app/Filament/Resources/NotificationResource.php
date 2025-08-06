<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                TextInput::make('title')->label('judul'),
                Select::make('user_id')
                    ->label('Pilih Man Power')
                    ->options(User::role('man_power')->pluck('name', 'id'))
                    ->default(fn($record) => $record?->user?->user_id)
                    ->searchable()
                    ->required(),
                TextArea::make('description')->label('Pesan'),
                Select::make('type')
                    ->label('Jenis Data') // Optional: field label
                    ->required()
                    ->options([
                        'submission' => 'Submission',
                        'absence' => 'Absence',
                        'message' => 'Message',
                    ])
                    ->native(false),
                
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make("title")->label('Judul'),
                TextColumn::make("user.user.name")->label('Dikirim ke'),
                TextColumn::make("description")->label('Pesan'),
                TextColumn::make('type')
    ->label('Kategori')
    ->badge()
    ->formatStateUsing(fn (string $state) => ucfirst($state)) // Kapital huruf pertama
    ->colors([
        'primary' => 'submission',
        'success' => 'absence',
        'danger' => 'message',
    ])
                
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
