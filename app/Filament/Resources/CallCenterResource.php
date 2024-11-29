<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\CallCenter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CallCenterResource\Pages;
use App\Filament\Resources\CallCenterResource\RelationManagers;
use App\Models\User;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Infolists\Components\FileEntry;
use Infolists\Components\ListEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfolistSection;


class CallCenterResource extends Resource
{
    protected static ?string $model = CallCenter::class;

    protected static ?string $modelLabel = 'Ouvidoria';

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
        return 'Ouvidoria';
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
                            // Protocolo
                            TextInput::make('protocolo')
                                ->label('Protocolo:')
                                ->placeholder('Número do Protocolo')
                                ->required()
                                ->columnSpan(3),

                            // Setor
                            Select::make('setor')
                                ->options([
                                    'Diretoria' => 'Diretoria',
                                    'CAJ' => 'CAJ',
                                    'COAFE' => 'COAFE',
                                    'CAFAB' => 'CAFAB',
                                ])
                                ->label('Setor Encarregada pela Resposta:')
                                ->required()
                                ->columnSpan(3),


                            TextInput::make('demandante')
                                ->label('Demandante:')
                                ->placeholder('Usuário que Registrou Queixa na Ouvidoria')
                                ->required()
                                ->disabled(fn($get) => $get('dado_sigiloso')) // Desabilita se 'dado_sigiloso' estiver ativado
                                ->columnSpan(2),

                            Toggle::make('dado_sigiloso')
                                ->label('Dado Sigiloso')
                                ->inline(false)
                                ->offColor('success') // Cor quando desativado
                                ->onColor('danger')  // Cor quando ativado
                                ->offIcon('heroicon-m-lock-open')
                                ->onIcon('heroicon-m-lock-closed')
                                ->reactive() // Torna o campo reativo
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        // Quando ativado, define "Dado Sigiloso"
                                        $set('demandante', 'Dado Sigiloso');
                                    } else {
                                        // Quando desativado, define um valor padrão
                                        $set('demandante', null);
                                    }
                                })
                                ->columnSpan(1),

                            // Unidade
                            TextInput::make('unidade')
                                ->label('Unidade:')
                                ->placeholder('Unidade envolvida na demanda')
                                ->columnSpan(3),

                            // Responsável da Aquisição
                            TextInput::make('resp_aquisicao')
                                ->label('Responsável da Aquisição:')
                                ->columnSpan(3),

                            // Data da última dispensação
                            DatePicker::make('date_dispensacao')
                                ->label('Data da última dispensação:')
                                ->columnSpan(3),
                        ])
                        ->columns(6),


                    Wizard\Step::make('Lista de Medicamentos')
                        ->schema([
                            Repeater::make('medicamentos')
                                ->label('Medicamentos')
                                ->schema([
                                    TextInput::make('medicamento')
                                        ->label('Adicionar Medicamento:')
                                        ->placeholder('Informe o nome do medicamento')
                                        ->required(),
                                ])
                                ->nullable() // Permite que o campo seja nulo
                                ->columns(1),

                        ]),


                    Wizard\Step::make('Observações')
                        ->schema([
                            Select::make('author_id')
                                ->label('Autor da Resposta:')
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable(),
                            DatePicker::make('date_resposta')
                                ->label('Data da resposta:')
                                ->minDate(now()->subYears(150))
                                ->maxDate(now()),

                            RichEditor::make('obs')
                                ->label('Campo de Observação:')
                                ->disableToolbarButtons([
                                    'blockquote',
                                    'strike',
                                ])
                                ->columnSpanFull(),

                        ])->columns(2),
                    Wizard\Step::make('Anexos')
                        ->schema([
                            FileUpload::make('file_espelho')
                                ->label('Anexo do Espelho:')
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(5128)
                                ->downloadable()
                                ->directory('callcenter_attachments')
                                ->disk('public')
                                ->visibility('private'),


                            FileUpload::make('attachments')
                                ->label('Anexos:')
                                ->multiple()
                                ->panelLayout('grid')
                                ->downloadable()
                                ->disk('public')  // Define o disco, podendo ser "public" ou outro disco configurado.
                                ->directory('callcenter_attachments'),  // Define um diretório específico para os anexos.


                        ]),


                ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('protocolo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('setor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resp_aquisicao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('demandante')
                    ->searchable(),
                Tables\Columns\IconColumn::make('dado_sigiloso')
                    ->boolean(),
                Tables\Columns\TextColumn::make('date_dispensacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_resposta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_create_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_id')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListCallCenters::route('/'),
            'create' => Pages\CreateCallCenter::route('/create'),
            'view' => Pages\ViewCallCenter::route('/{record}'),
            'edit' => Pages\EditCallCenter::route('/{record}/edit'),
        ];
    }
}
