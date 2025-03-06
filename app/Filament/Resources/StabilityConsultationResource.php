<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Forms\Components\TextInput\Mask;
use App\Models\StabilityConsultation;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Leandrocfe\FilamentPtbrFormFields\Document;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StabilityConsultationResource\Pages;
use App\Filament\Resources\StabilityConsultationResource\RelationManagers;
use App\Models\User;
use Filament\Infolists;
use App\Models\CallCenter;
use Illuminate\Support\HtmlString;
use Infolists\Components\FileEntry;
use Infolists\Components\ListEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Group;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Illuminate\Database\Eloquent\Model;

class StabilityConsultationResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('estabelecimento') // Carrega o relacionamento corretamente
            ->when(!auth()->user()->hasRole('super_admin'), function (Builder $query) {
                $query->where('estabelecimento_id', auth()->user()->estabelecimento_id);
            });
    }


    protected static ?string $model = StabilityConsultation::class;

    protected static ?string $modelLabel = 'Excursão de Temperatura';

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();

        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('estabelecimento_id', auth()->user()->estabelecimento_id);
        }

        return $query->count();
    }


    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-plus-circle';
    }
    public static function getNavigationLabel(): string
    {
        return 'Excursão de Temperatura';
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
                                ->default(fn() => StabilityConsultation::find(request()->route('record'))?->estabelecimento?->nome ?? 'Não informado')
                                ->disabled() // Se não for para ser editável
                                ->required()
                                ->helperText('Nome da unidade')
                                ->maxLength(255),
                            Document::make('cnpj')
                                ->label('CNPJ:')
                                ->rule('cnpj')
                                ->required()
                                ->validation(false)  // Remover em produção
                                ->cnpj('99999999/9999-99')
                                ->helperText('Insira o CNPJ no formato: 00000000/0000-00'),
                            // Campo de verificação da excursão de temperatura
                            Forms\Components\DateTimePicker::make('excursion_verification_at')
                                ->label('Excursão de temperatura')
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
                                            ? $start->diffInHours($end)
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
                                            ? $start->diffInHours($end)
                                            : 0; // Retorna 0 se a data inicial for posterior
                                        $set('estimated_exposure_time', $difference);
                                    }
                                }),
                            Forms\Components\TextInput::make('estimated_exposure_time')
                                ->label('Tempo Estimado de Exposição')
                                ->helperText('Tempo de exposição estimada à temperatura não recomendada em horas.')
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    // Atualiza o campo com o valor calculado
                                    if ($get('last_verification_at') && $get('returned_to_storage_at')) {
                                        $start = now()->parse($get('last_verification_at'));
                                        $end = now()->parse($get('returned_to_storage_at'));
                                        $difference = $start->lessThanOrEqualTo($end)
                                            ? $start->diffInHours($end)
                                            : 0; // Retorna 0 se a data inicial for posterior
                                        $set('estimated_exposure_time', $difference);  // Atualiza o valor do campo
                                    }
                                }),

                        ])
                        ->columns(2),

                    Wizard\Step::make('Dados de Exposição')
                        ->schema([
                            Forms\Components\TextInput::make('max_exposed_temperature')
                                ->label('Temperatura Máxima Exposta')
                                ->helperText('Insira a temperatura máxima exposta registrada.')
                                ->rule('between:-50,100', 'A temperatura deve estar entre -50°C e 100°C.')
                                ->numeric(),
                            Forms\Components\TextInput::make('min_exposed_temperature')
                                ->label('Temperatura Mínima Exposta')
                                ->helperText('Insira a temperatura mínima exposta registrada.')
                                ->rule('between:-50,100', 'A temperatura deve estar entre -50°C e 100°C.')
                                ->numeric(),
                            Repeater::make('medicament')
                                ->label('Medicamentos')
                                ->schema([
                                    Forms\Components\TextInput::make('medicament_name')
                                        ->label('Nome do Medicamento')
                                        ->columnSpan(2)
                                        ->required(),
                                    Forms\Components\TextInput::make('medicament_manufacturer')
                                        ->columnSpan(1)
                                        ->label('Fabricante do Medicamento')
                                        ->required(),
                                    Forms\Components\TextInput::make('medicament_batch')
                                        ->label('Lote do Medicamento')
                                        ->required(),
                                    Forms\Components\DatePicker::make('medicament_date')
                                        ->label('Data de Validade')
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
                            Forms\Components\RichEditor::make('observations')
                                ->label('Observações')
                                ->columnSpanFull(),
                            FileUpload::make('file_monitor_temp')
                                ->label('Monitoramento de Temperatura')
                                ->columnSpanFull()
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(5128)
                                ->downloadable()
                                ->directory('stabilityConsultation_attachments')
                                ->disk('s3')
                                ->visibility('publico'),
                        ]),
                    Wizard\Step::make('Informações do Laboratório')
                        ->schema([
                            // Toggle que define a resposta do laboratório
                            Toggle::make('resp_laboratory')
                                ->label('Houve resposta do Laboratório:')
                                ->inline(false)
                                ->offColor('danger') // Cor quando desativado
                                ->onColor('success')  // Cor quando ativado
                                ->offIcon('heroicon-m-x-mark')
                                ->onIcon('heroicon-m-check')
                                ->reactive() // Torna o campo reativo
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                    } else {
                                        $set('text_laboratory', null); // Reseta o texto
                                        $set('text_unidade', null); // Reseta o texto
                                    }
                                }),

                            // Organiza os campos lado a lado
                            Forms\Components\Grid::make(2) // Define 2 colunas
                                ->schema([
                                    // Campo para observações do laboratório
                                    Forms\Components\RichEditor::make('text_laboratory')
                                        ->label('Observações do Laboratório')
                                        ->toolbarButtons([
                                            'blockquote',
                                            'bold',
                                            'bulletList',
                                            'h2',
                                            'h3',
                                            'italic',
                                            'link',
                                            'orderedList',
                                            'underline',
                                            'undo',
                                        ])
                                        ->requiredIf('resp_laboratory', true)  // Obrigatório se o toggle estiver ativado
                                        ->hidden(fn($get) => !$get('resp_laboratory')) // Esconde se o toggle estiver desativado
                                        ->columnSpan(1), // Ocupa uma coluna

                                    // Campo para observações da unidade
                                    Forms\Components\RichEditor::make('text_unidade')
                                        ->label('Observações da Unidade')
                                        ->toolbarButtons([
                                            'blockquote',
                                            'bold',
                                            'bulletList',
                                            'h2',
                                            'h3',
                                            'italic',
                                            'link',
                                            'orderedList',
                                            'underline',
                                            'undo',
                                        ])
                                        ->requiredIf('resp_laboratory', true)  // Obrigatório se o toggle estiver ativado
                                        ->hidden(fn($get) => !$get('resp_laboratory')) // Esconde se o toggle estiver desativado
                                        ->columnSpan(1), // Ocupa uma coluna
                                ]),

                        ]),
                ])->columnSpan('full')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('protocol_number')
                    ->label('Protocolo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution_name')
                    ->label('Nome da Instituição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('excursion_verification_at')
                    ->label('Data da Excursão')
                    ->dateTime('d/m/Y')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(StabilityConsultation $record) => route('pdf', $record))
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    EditAction::make()
                        ->color('warning'),
                    ActivityLogTimelineTableAction::make('Logs')
                        ->color('info')
                        ->timelineIcons([
                            'created' => 'heroicon-m-check-badge',
                            'updated' => 'heroicon-m-pencil-square',
                        ])
                        ->timelineIconColors([
                            'created' => 'info',
                            'updated' => 'warning',
                        ]),
                    DeleteAction::make()
                        ->successNotificationTitle('Deletado com sucesso.'),
                ])->tooltip('Opções'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            // Primeira seção: Detalhes principais
            InfolistSection::make([
                Fieldset::make('Detalhes Gerais')
                    ->schema([
                        TextEntry::make('protocol_number')
                            ->label('Número do Protocolo:')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold')
                            ->columnSpan(1)
                            ->copyable()
                            ->copyMessage('Copiado!'),
                        TextEntry::make('institution_name')
                            ->label('Nome da Instituição:')

                            ->columnSpan(1)
                            ->copyable()
                            ->copyMessage('Copiado!'),
                        TextEntry::make('cnpj')
                            ->label('CNPJ:')
                            ->columnSpan(1)
                            ->copyable()
                            ->copyMessage('Copiado!'),
                        TextEntry::make('excursion_verification_at')
                            ->label('Excursão de Temperatura')
                            ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'Não Informada')
                            ->columnSpan(1),
                        TextEntry::make('last_verification_at')
                            ->label('Última Verificação')
                            ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'Não Informada')
                            ->columnSpan(1),
                        TextEntry::make('returned_to_storage_at')
                            ->label('Retorno ao Armazenamento')
                            ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'Não Informada')
                            ->columnSpan(1),
                        TextEntry::make('estimated_exposure_time')
                            ->label('Tempo Estimado de Exposição (min)')
                            ->placeholder('Verifique datas nos campos: Última Verificação e Retorno ao Armazenamento')
                            ->columnSpan(2),
                    ])
                    ->columns(3),

                Fieldset::make('Informações de Exposição')
                    ->schema([
                        TextEntry::make('max_exposed_temperature')
                            ->label('Temperatura Máxima Exposta (°C)')
                            ->columnSpan(1),
                        TextEntry::make('min_exposed_temperature')
                            ->label('Temperatura Mínima Exposta (°C)')
                            ->columnSpan(1),

                        Infolists\Components\RepeatableEntry::make('medicament')
                            ->label('Medicamentos')
                            ->schema([
                                TextEntry::make('medicament_name')
                                    ->label('Nome do Medicamento')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->columnSpan(2),
                                TextEntry::make('medicament_manufacturer')
                                    ->label('Fabricante'),
                                TextEntry::make('medicament_batch')
                                    ->label('Lote'),
                                TextEntry::make('medicament_date')
                                    ->label('Data de Validade')
                                    ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'Não Informada'),
                                TextEntry::make('medicament_quantity')
                                    ->label('Quantidade'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Fieldset::make('Informações do Laboratório')
                    ->schema([
                        TextEntry::make('text_laboratory')
                            ->label('Observações do Laboratório')
                            ->placeholder('Sem resposta do laboratório.')
                            ->columnSpan(1),
                        TextEntry::make('text_unidade')
                            ->label('Observações da Unidade')
                            ->placeholder('Sem resposta do unidade.')
                            ->columnSpan(1),
                    ])
                    ->columns(1),
            ])->columnSpan(2),


            // Segunda seção: Informações adicionais
            InfolistSection::make([
                Group::make([
                    TextEntry::make('created_at')
                        ->label('Criado em:')
                        ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'Não Informada'),
                    TextEntry::make('updated_at')
                        ->label('Atualizado em:')
                        ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'Não Informada'),
                ])
                    ->columns(2),
                Group::make([
                    TextEntry::make('creator.name')
                        ->label('Responsável')
                        ->color('primary'),
                    TextEntry::make('creator.cargo.name')
                        ->label('Cargo')
                        ->color('info'),
                ])
                    ->columns(2),

                TextEntry::make('file_monitor_temp')
                    ->label('Monitoramento de Temperatura')
                    ->columnSpanFull()
                    ->placeholder('Sem anexos')
                    ->listWithLineBreaks()->bulleted()
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return 'Sem anexo do espelho';
                        }

                        $url = Storage::disk('s3')->url($state);

                        return sprintf(
                            '<span style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);"
                class="text-xs rounded-md mx-1 font-medium px-2 min-w-[theme(spacing.6)] py-1
                bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10
                dark:text-custom-400 dark:ring-custom-400/30">
                <a href="%s" target="_blank">%s</a>
            </span>',
                            $url,
                            basename($state)
                        );
                    })
                    ->html(),
            ])
                ->columnSpan(1),
        ])
            ->columns(3); // Define o layout com três colunas
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
