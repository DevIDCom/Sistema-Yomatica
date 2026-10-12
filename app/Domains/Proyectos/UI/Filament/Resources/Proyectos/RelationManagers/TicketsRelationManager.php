<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers;

use App\Domains\Proyectos\Domain\StateMachines\Project\Active;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Tickets';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $inverseRelationship = 'project';
    protected function projectIsActive(): bool
    {
        return $this->getOwnerRecord()->status->equals(Active::class);
    }

    public function getTableDescription(): ?string
    {
        return $this->projectIsActive()
            ? null
            : 'El proyecto no está activo. Actívalo para registrar o vincular tickets.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reporte')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Título del ticket')
                            ->placeholder('Ej: Falla en unidad condensadora, piso 3')
                            ->required()
                            ->minLength(5)
                            ->maxLength(200)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->placeholder('Detalla qué se observó, desde cuándo y cualquier antecedente relevante.')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                'low'    => 'Baja',
                                'medium' => 'Media',
                                'high'   => 'Alta',
                                'urgent' => 'Urgente',
                            ])
                            ->default('medium')
                            ->native(false)
                            ->required(),

                        Select::make('area_id')
                            ->label('Área')
                            ->relationship('area', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => ($record->branch?->name ? $record->branch->name . ' — ' : '') . $record->name
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Sin área'),

                        Select::make('contact_id')
                            ->label('Contacto que reporta')
                            ->relationship(
                                name: 'contact',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where(
                                    'client_id',
                                    $this->getOwnerRecord()->client_id
                                ),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => $record->name . ($record->role_title ? " ({$record->role_title})" : '')
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Sin contacto'),
                    ]),

                Section::make('Asignación y programación')
                    ->columns(2)
                    ->schema([
                        Select::make('assigned_tech_id')
                            ->label('Técnico Asignado')
                            ->relationship(
                                name: 'technician',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->onlyTechnicians(),
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Sin asignar'),

                        TextInput::make('economic_weight')
                            ->label('Peso económico')
                            ->placeholder('Ej: 1500.00')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->maxValue(99999999.99)
                            ->step(0.01)
                            ->default(0),

                        DateTimePicker::make('scheduled_at')
                            ->label('Programado para')
                            ->placeholder('Sin programar')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->native(false),

                        DateTimePicker::make('due_at')
                            ->label('Fecha compromiso (SLA)')
                            ->placeholder('Sin compromiso')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->native(false)
                            ->afterOrEqual('scheduled_at')
                            ->validationMessages([
                                'after_or_equal' => 'El compromiso no puede ser anterior a la programación.',
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
            ->columns([
                TextColumn::make('code')
                    ->label('Folio')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->placeholder('Sin folio'),

                TextColumn::make('title')
                    ->label('Ticket')
                    ->searchable()
                    ->wrap()
                    ->limit(60)
                    ->description(fn ($record) => str($record->description)->limit(80)),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '—')
                    ->color(fn ($state) => $state?->color() ?? 'gray')
                    ->icon(fn ($state) => $state?->icon()),

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'low'    => 'Baja',
                        'medium' => 'Media',
                        'high'   => 'Alta',
                        'urgent' => 'Urgente',
                        default  => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'low'    => 'gray',
                        default  => 'info',
                    }),

                TextColumn::make('area.name')
                    ->label('Área')
                    ->placeholder('Sin área')
                    ->description(fn ($record) => $record->area?->branch?->name),

                TextColumn::make('technician.name')
                    ->label('Técnico')
                    ->placeholder('Sin asignar')
                    ->searchable(),

                TextColumn::make('contact.name')
                    ->label('Reportó')
                    ->placeholder('Sin contacto')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('scheduled_at')
                    ->label('Programado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin programar')
                    ->sortable(),

                TextColumn::make('due_at')
                    ->label('Compromiso')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin SLA')
                    ->color(fn ($record) => $record->due_at && $record->due_at->isPast() && ! $record->finished_at ? 'danger' : null)
                    ->sortable(),

                TextColumn::make('evidences_count')
                    ->label('Evidencias')
                    ->counts('evidences')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'low'    => 'Baja',
                        'medium' => 'Media',
                        'high'   => 'Alta',
                        'urgent' => 'Urgente',
                    ]),

                SelectFilter::make('assigned_tech_id')
                    ->label('Técnico')
                    ->relationship('technician', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Nuevo ticket')
                    ->disabled(fn () => ! $this->projectIsActive())
                    ->tooltip(fn () => $this->projectIsActive()
                        ? null
                        : 'Activa el proyecto para poder crear tickets.')
                    ->mutateDataUsing(function (array $data, CreateAction $action): array {
                        $project = $this->getOwnerRecord();

                        if (! $this->projectIsActive()) {
                            Notification::make()
                                ->title('El proyecto no está activo')
                                ->body('Actívalo antes de registrar tickets.')
                                ->warning()
                                ->send();

                            $action->halt();
                        }

                        $uuid = $project->client?->uuid;

                        if (blank($uuid)) {
                            Notification::make()
                                ->title('El proyecto no tiene cliente asignado')
                                ->body('Asigna un cliente al proyecto antes de crear tickets.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        $data['client_uuid'] = $uuid;
                        $data['created_by']  = auth()->id();

                        return $data;
                    }),

                AssociateAction::make()
                    ->label('Vincular ticket existente')
                    ->recordTitleAttribute('title')
                    ->preloadRecordSelect()
                    ->disabled(fn () => ! $this->projectIsActive())
                    ->tooltip(fn () => $this->projectIsActive()
                        ? null
                        : 'Activa el proyecto para poder vincular tickets.'),

                Action::make('crearEnPanelTickets')
                    ->label('Crear en panel de Tickets')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->disabled(fn () => ! $this->projectIsActive())
                    ->tooltip(fn () => $this->projectIsActive()
                        ? null
                        : 'Activa el proyecto para poder crear tickets.')
                    ->url(function () {
                        $project = $this->getOwnerRecord();

                        return TicketResource::getUrl('create', [
                            'proyecto_id'  => $project->getKey(),
                            'cliente_uuid' => $project->client?->uuid,
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make()->label('Desvincular'),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin tickets')
            ->emptyStateDescription('Los tickets de campo de este proyecto aparecerán aquí.');
    }
}
