<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets\Schemas;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Open;
use App\Domains\Tickets\Domain\StateMachines\Ticket\TicketState;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Ticket')
                    ->description('Detalles generales, prioridad económica y estado operativo del servicio.')
                    ->icon('heroicon-o-ticket')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        TextInput::make('title')
                            ->label('Título / Asunto')
                            ->required()
                            ->minLength(5)
                            ->maxLength(200)
                            ->columnSpan(6),

                        Select::make('status')
                            ->label('Estado Operativo')
                            ->options(TicketState::getSelectOptions())
                            ->default(Open::$name)
                            ->selectablePlaceholder(false)
                            ->native(false)
                            ->required()
                            ->columnSpan(3),

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
                            ->required()
                            ->columnSpan(3),

                        Textarea::make('description')
                            ->label('Descripción de la falla')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpan(12),

                        TextInput::make('economic_weight')
                            ->label('Peso Económico')
                            ->numeric()
                            ->prefix('$')
                            ->default(1.00)
                            ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                            ->required()
                            ->columnSpan(4),

                        DateTimePicker::make('scheduled_at')
                            ->label('Programado para')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->native(false)
                            ->columnSpan(4),

                        DateTimePicker::make('due_at')
                            ->label('Fecha compromiso (SLA)')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->native(false)
                            ->afterOrEqual('scheduled_at')
                            ->columnSpan(4),
                    ]),

                Section::make('Ubicación y Asignación')
                    ->description('Vinculación con la entidad cliente, áreas de la planta y personal asignado.')
                    ->icon('heroicon-o-user-group')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        Select::make('client_uuid')
                            ->label('Cliente')
                            ->options(fn () => CrmClient::query()->orderBy('business_name')->pluck('business_name', 'uuid'))
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('area_id', null);
                                $set('contact_id', null);
                            })
                            ->columnSpan(6),

                        Select::make('project_id')
                            ->label('Proyecto')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(6),

                        Select::make('area_id')
                            ->label('Área')
                            ->options(function (callable $get) {
                                $clientUuid = $get('client_uuid');
                                if (blank($clientUuid)) return [];

                                return CrmArea::query()
                                    ->whereHas('branch.client', fn ($q) => $q->where('uuid', $clientUuid))
                                    ->with('branch')
                                    ->get()
                                    ->mapWithKeys(fn ($area) => [
                                        $area->id => ($area->branch?->name ? $area->branch->name . ' — ' : '') . $area->name,
                                    ]);
                            })
                            ->searchable()
                            ->native(false)
                            ->columnSpan(4),

                        Select::make('contact_id')
                            ->label('Contacto que reporta')
                            ->options(function (callable $get) {
                                $clientUuid = $get('client_uuid');
                                if (blank($clientUuid)) return [];

                                return CrmContact::query()
                                    ->whereHas('client', fn ($q) => $q->where('uuid', $clientUuid))
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->native(false)
                            ->columnSpan(4),

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
                            ->columnSpan(4),
                    ]),
            ]);
    }
}
