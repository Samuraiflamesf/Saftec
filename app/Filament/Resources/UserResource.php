<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Cargo;
use App\Models\Estabelecimento;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Usuário';

    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationLabel(): string
    {
        return 'Lista de Usuários';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Configurações';
    }
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nome Completo:')
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->label(
                        'E-mail:'
                    )
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->label('Senha:')
                    ->revealable()
                    ->minLength(8)
                    ->nullable()
                    ->hidden(fn(string $operation) => in_array($operation, ['edit', 'view']))
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                Forms\Components\Select::make('roles')
                    ->label('Perfil do usuário:')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->searchable(),
                Select::make('cargo_id')
                    ->label('Cargo/Função')
                    ->relationship('cargo', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('estabelecimento_id')
                    ->label('Estabelecimento')
                    ->relationship(name: 'estabelecimento', titleAttribute: 'nome')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('cnes')
                            ->label('CNES')
                            ->numeric()
                            ->required()
                            ->maxLength(8),
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome')
                            ->required()
                            ->maxLength(70),
                        Select::make('macrorregiao')
                            ->label('Macrorregião')
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
                            ]),
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Completo')
                    ->searchable(),
                TextColumn::make('email')
                    ->label(
                        'E-mail'
                    )
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->label(
                        'Criado em'
                    )
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Logs')
                    ->timelineIcons([
                        'created' => 'heroicon-m-check-badge',
                        'updated' => 'heroicon-m-pencil-square',
                    ])
                    ->timelineIconColors([
                        'created' => 'info',
                        'updated' => 'warning',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
