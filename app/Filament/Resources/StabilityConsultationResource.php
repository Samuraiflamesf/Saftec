<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Forms\Components\TextInput\Mask;
use App\Models\StabilityConsultation;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StabilityConsultationResource\Pages;
use App\Filament\Resources\StabilityConsultationResource\RelationManagers;

class StabilityConsultationResource extends Resource
{
    protected static ?string $model = StabilityConsultation::class;

    protected static ?string $modelLabel = 'Temperatura';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-plus-circle';
    }
    public static function getNavigationLabel(): string
    {
        return 'Temperatura';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Administração';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Detalhes Gerais')
                        ->schema([
                            Forms\Components\TextInput::make('institution_name')
                                ->label('Nome da Instituição:')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('cnpj')
                                ->label('CNPJ:')
                                ->required()
                                ->maxLength(18) // Define o comprimento máximo como 18 (com máscara incluída)
                                ->rule('cnpj') // Aplica uma regra de validação customizada, se disponível
                                ->helperText('Insira o CNPJ no formato: 00.000.000/0000-00'),
                            // Campo de verificação da excursão de temperatura
                            Forms\Components\DateTimePicker::make('excursion_verification_at')
                                ->label('Verificação da Excursão de temperatura')
                                ->helperText('Data e horário da verificação da excursão de temperatura.')
                                ->required()
                                ->seconds(false)
                                ->live(onBlur: true),


                            Forms\Components\DateTimePicker::make('last_verification_at')
                                ->label('Última Verificação')
                                ->helperText('Data e horário da última verificação antes da excursão de temperatura.')
                                ->required()
                                ->seconds(false)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    // Atualiza o campo estimado ao modificar `last_verification_at`
                                    if ($state && $get('returned_to_storage_at')) {
                                        $start = now()->parse($state);
                                        $end = now()->parse($get('returned_to_storage_at'));
                                        $difference = $start->lessThanOrEqualTo($end)
                                            ? $start->diffInMinutes($end)
                                            : 0; // Retorna 0 se a data inicial for posterior
                                        $set('estimated_exposure_time', $difference);
                                    }
                                }),

                            Forms\Components\DateTimePicker::make('returned_to_storage_at')
                                ->label('Retorno ao Armazenamento')
                                ->helperText('Data e horário em que o item retornou à condição preconizada de armazenamento.')
                                ->required()
                                ->seconds(false)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    // Atualiza o campo estimado ao modificar `returned_to_storage_at`
                                    if ($state && $get('last_verification_at')) {
                                        $start = now()->parse($get('last_verification_at'));
                                        $end = now()->parse($state);
                                        $difference = $start->lessThanOrEqualTo($end)
                                            ? $start->diffInMinutes($end)
                                            : 0; // Retorna 0 se a data inicial for posterior
                                        $set('estimated_exposure_time', $difference);
                                    }
                                }),

                            Forms\Components\TextInput::make('estimated_exposure_time')
                                ->label('Tempo Estimado de Exposição')
                                ->helperText('Tempo de exposição estimada à temperatura não recomendada em minutos.')
                                ->disabled(),


                        ])
                        ->columns(2),

                    Wizard\Step::make('Dados de Exposição')
                        ->schema([
                            Forms\Components\TextInput::make('max_exposed_temperature')
                                ->label('Temperatura Máxima Exposta')
                                ->numeric(),
                            Forms\Components\TextInput::make('min_exposed_temperature')
                                ->label('Temperatura Mínima Exposta')
                                ->numeric(),
                            Repeater::make('medicamentos')
                                ->label('Medicamentos')
                                ->schema([
                                    Forms\Components\TextInput::make('medicament_name')
                                        ->label('Nome do Medicamento')
                                        ->columns(2)
                                        ->required(),
                                    Forms\Components\TextInput::make('medicament_manufacturer')
                                        ->columns(1)
                                        ->label('Fabricante do Medicamento')
                                        ->required(),
                                    Forms\Components\TextInput::make('medicament_batch')
                                        ->label('Lote do Medicamento')
                                        ->required(),
                                    Forms\Components\DatePicker::make('medicament_date')
                                        ->label('Data do Medicamento')
                                        ->required(),
                                    Forms\Components\TextInput::make('medicament_quantity')
                                        ->label('Quantidade do Medicamento')
                                        ->numeric()
                                        ->required(),
                                ])
                                ->nullable() // Permite que o campo seja nulo
                                ->columnSpanFull()
                                ->columns(3),

                        ])
                        ->columns(2),

                    Wizard\Step::make('Informações do Pedido')
                        ->schema([
                            Forms\Components\TextInput::make('order_number')
                                ->label('Número do Pedido')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('distribution_number')
                                ->label('Número de Distribuição')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('observations')
                                ->label('Observações')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('file_monitor_temp')
                                ->label('Monitoramento de Temperatura')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan('full')
                    ->columns(2),
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
