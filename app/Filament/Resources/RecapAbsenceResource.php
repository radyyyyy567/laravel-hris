<?php

namespace App\Filament\Resources;

use App\Models\Absence;
use App\Filament\Resources\RecapAbsenceResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecapAbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Absensi';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $label = 'Rekap Absensi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Optional form for editing or showing details
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('manpower.user.name')->label('Man Power'),
                TextColumn::make('manpower.user.nip')->label('NIP'),
                TextColumn::make('name')->label('Di Buat'),
                TextColumn::make('checkin_time')->label('Jam Masuk'),
                TextColumn::make('checkout_time')->label('Jam Keluar'),
                TextColumn::make('long_lat')->label('Lokasi'),
                TextColumn::make('description')->label('Keterangan'),
                TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                // ✅ Example Filters

                Filter::make('date')
                    ->label('Tanggal Hari Ini')
                    ->query(fn(Builder $query): Builder => $query->whereDate('created_at', now()->toDateString())),

                Filter::make('status_hadir')
                    ->label('Status: Hadir')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'hadir')),

                Filter::make('status_izin')
                    ->label('Status: Izin')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'izin')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Optional, can be removed if Recap is read-only
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Optional
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecapAbsences::route('/'),
            'create' => Pages\CreateRecapAbsence::route('/create'),
            'edit' => Pages\EditRecapAbsence::route('/{record}/edit'),
        ];
    }
}
