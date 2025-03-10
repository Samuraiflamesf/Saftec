<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Medicament;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MedicamentResource\Pages;
use App\Filament\Resources\MedicamentResource\RelationManagers;

class MedicamentResource extends Resource
{
    protected static ?string $model = Medicament::class;
    protected static ?string $navigationIcon = 'lucide-pill';

    public static function getNavigationIcon(): string
    {
        return 'lucide-pill';
    }
    protected static ?string $modelLabel = 'Medicamento';
    public static function getNavigationLabel(): string
    {
        return 'Lista de Medicamentos';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Cadastros';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome Completo do Medicamento')
                    ->required()
                    ->helperText('Informe o nome com base no SIMPAS'),
                TextInput::make('simpas')
                    ->label('Codigo SIMPAS')
                    ->helperText('Informe o código SIMPAS do medicamento'),
                Textarea::make('observation')
                    ->label('Texto padrão para analise de bula')
                    ->helperText('Informe o texto padrão'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do Medicamento')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('simpas')
                    ->label('Codigo SIMPAS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observation')
                    ->limit(20)
                    ->label('Texto padrão'),
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
            'index' => Pages\ListMedicaments::route('/'),
            'create' => Pages\CreateMedicament::route('/create'),
            'view' => Pages\ViewMedicament::route('/{record}'),
            'edit' => Pages\EditMedicament::route('/{record}/edit'),
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
