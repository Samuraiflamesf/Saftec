<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StabilityConsultationResource\Pages;
use App\Filament\Resources\StabilityConsultationResource\RelationManagers;
use App\Models\StabilityConsultation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StabilityConsultationResource extends Resource
{
    protected static ?string $model = StabilityConsultation::class;

    protected static ?string $modelLabel = 'Estabilidade de Temperatura';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-bookmark';
    }
    public static function getNavigationLabel(): string
    {
        return 'Estabilidade de Temperatura';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Administração';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('institution_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cnpj')
                    ->required()
                    ->maxLength(18),
                Forms\Components\DateTimePicker::make('last_verification_at'),
                Forms\Components\DateTimePicker::make('excursion_verification_at'),
                Forms\Components\TextInput::make('estimated_exposure_time')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('returned_to_storage_at'),
                Forms\Components\TextInput::make('max_exposed_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('min_exposed_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('medicament_name')
                    ->required(),
                Forms\Components\TextInput::make('medicament_manufacturer')
                    ->required(),
                Forms\Components\TextInput::make('medicament_batch')
                    ->required(),
                Forms\Components\TextInput::make('medicament_date')
                    ->required(),
                Forms\Components\TextInput::make('medicament_quantity')
                    ->required(),
                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('distribution_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('observations')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('filled_by')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('role')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('file_monitor_temp')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('protocol_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('institution_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_verification_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('excursion_verification_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_exposure_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('returned_to_storage_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_exposed_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_exposed_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('distribution_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('filled_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('protocol_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStabilityConsultations::route('/'),
            'create' => Pages\CreateStabilityConsultation::route('/create'),
            'view' => Pages\ViewStabilityConsultation::route('/{record}'),
            'edit' => Pages\EditStabilityConsultation::route('/{record}/edit'),
        ];
    }
}
