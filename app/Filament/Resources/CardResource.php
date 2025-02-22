<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Card;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CardResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CardResource\RelationManagers;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static ?string $modelLabel = 'Dashboard';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-cube';
    }
    public static function getNavigationLabel(): string
    {
        return 'Gerenciador de Card';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Administração';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Card')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nome')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(35)
                                    ->columnSpan(1),

                                Select::make('tipo')
                                    ->label('Tipo')
                                    ->options([
                                        'dashboard' => 'Dashboard',
                                        'ferramenta' => 'Ferramenta',
                                    ])
                                    ->required()
                                    ->default('dashboard')
                                    ->columnSpan(1),
                            ]),

                        Textarea::make('descricao')
                            ->label('Descrição')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),

                        TextInput::make('link')
                            ->label('Link')
                            ->url()
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('imagem')
                            ->label('Imagem do Card')
                            ->image()
                            ->disk('s3')  // Define o disco, podendo ser "public" ou outro disco configurado.
                            ->directory('cards')
                            ->required()
                            ->columnSpanFull(),

                    ]),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagem'),
                TextColumn::make('nome')->searchable(),
                TextColumn::make('descricao')->limit(50),
                TextColumn::make('link'),
                TextColumn::make('tipo'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
