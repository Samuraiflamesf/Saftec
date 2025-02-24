<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Estabelecimento;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EstabelecimentoResource\Pages;
use App\Filament\Resources\EstabelecimentoResource\RelationManagers;

class EstabelecimentoResource extends Resource
{
    protected static ?string $model = Estabelecimento::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $modelLabel = 'Estabelecimento';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-building-office-2';
    }
    public static function getNavigationLabel(): string
    {
        return 'Estabelecimentos';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Administração';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cnes')
                    ->label('CNES')
                    ->numeric()
                    ->required()
                    ->maxLength(8),
                Forms\Components\TextInput::make('nome')
                    ->label(
                        'Nome'
                    )
                    ->required()
                    ->maxLength(70),
                Select::make('macrorregiao')
                    ->label(
                        'Macrorregião'
                    )
                    ->searchable()
                    ->required()
                    ->options([
                        'Centro-Leste' => 'Centro-Leste',
                        'Centro-Norte' => 'Centro-Norte',
                        'Extremo-Sul' => 'Extremo Sul',
                        'Leste' => 'Leste',
                        'Nordeste' => 'Nordeste',
                        'Norte' => 'Norte',
                        'Oeste' => 'Oeste',
                        'Sudoeste' => 'Sudoeste',
                        'Sul' => 'Sul',
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cnes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nome')
                    ->sortable()
                    ->words(4)
                    ->searchable(),
                Tables\Columns\TextColumn::make('macrorregiao')
                    ->searchable(),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListEstabelecimentos::route('/'),
            'create' => Pages\CreateEstabelecimento::route('/create'),
            'view' => Pages\ViewEstabelecimento::route('/{record}'),
            'edit' => Pages\EditEstabelecimento::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
