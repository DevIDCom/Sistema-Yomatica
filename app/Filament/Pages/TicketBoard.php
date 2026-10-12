<?php

namespace App\Filament\Pages;

use App\Domains\Clientes\Infraestructure\Eloquet\Models\CrmClient;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Cancelled;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Closed;
use App\Domains\Tickets\Domain\StateMachines\Ticket\InProgress;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Open;
use App\Domains\Tickets\Domain\StateMachines\Ticket\Resolved;
use App\Domains\Tickets\Domain\StateMachines\Ticket\TicketState;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Spatie\ModelStates\Exceptions\TransitionNotFound;

class TicketBoard extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';
    protected static string|null|\UnitEnum $navigationGroup = 'Soporte';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Tablero';
    protected static ?string $title = 'Tickets Kanban';
    protected string $view = 'filament.pages.ticket-board';

    public string $viewMode = 'status';
    public string $searchQuery = '';

    public function createTicketAction(): Action
    {
        return Action::make('createTicket')
            ->label('Nuevo Ticket')
            ->modalHeading('Crear Nuevo Ticket')
            ->modalWidth('lg')
            ->form([
                TextInput::make('title')
                    ->label('Título del ticket')
                    ->required()
                    ->maxLength(255),

                Select::make('client_uuid')
                    ->label('Cliente')
                    ->options(fn () => CrmClient::pluck('buisness_name', 'uuid'))
                    ->searchable()
                    ->nullable(),

                Select::make('project_id')
                    ->label('Proyecto')
                    ->options(fn () => FsmProject::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Select::make('area_id')
                    ->label('Área')
                    ->options(fn () => CrmArea::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Select::make('assigned_tech_id')
                    ->label('Asignar a técnico')
                    ->options(fn () => User::role('tecnico')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                TextInput::make('economic_weight')
                    ->label('Peso Económico')
                    ->numeric()
                    ->default(1.00),
            ])
            ->action(function (array $data): void {
                $data['created_by'] = auth()->id();

                FsmTicket::create($data);

                Notification::make()
                    ->title('Ticket creado con éxito')
                    ->success()
                    ->send();
            });
    }

    public function editTicketAction(): Action
    {
        return Action::make('editTicket')
            ->label('Detalles del Ticket')
            ->modalHeading(fn (array $arguments) => 'Editar Ticket #' . ($arguments['ticketId'] ?? ''))
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Guardar Cambios')
            ->fillForm(function (array $arguments): array {
                $ticket = FsmTicket::find($arguments['ticketId'] ?? null);

                if (! $ticket) {
                    return [];
                }

                return [
                    'id'               => $ticket->id,
                    'title'            => $ticket->title,
                    'client_uuid'      => $ticket->client_uuid,
                    'project_id'       => $ticket->project_id,
                    'area_id'          => $ticket->area_id,
                    'assigned_tech_id' => $ticket->assigned_tech_id,
                    'economic_weight'  => $ticket->economic_weight,
                    'status'           => $ticket->status::$name ?? Open::$name,
                ];
            })
            ->form([
                TextInput::make('id')
                    ->label('Folio ID')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('title')
                    ->label('Título del ticket')
                    ->required()
                    ->maxLength(255),

                Select::make('client_uuid')
                    ->label('Cliente')
                    ->options(fn () => CrmClient::pluck('business_name', 'uuid'))
                    ->searchable()
                    ->nullable(),

                Select::make('project_id')
                    ->label('Proyecto')
                    ->options(fn () => FsmProject::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Select::make('area_id')
                    ->label('Área')
                    ->options(fn () => CrmArea::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Select::make('assigned_tech_id')
                    ->label('Técnico Asignado')
                    ->options(fn () => User::role('tecnico')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                TextInput::make('economic_weight')
                    ->label('Peso Económico')
                    ->numeric(),

                Select::make('status')
                    ->label('Estatus del Ticket')
                    ->options(TicketState::getSelectOptions())
                    ->required(),
            ])
            ->action(function (array $data, array $arguments): void {
                $ticket = FsmTicket::find($arguments['ticketId'] ?? null);

                if (! $ticket) {
                    return;
                }

                $ticket->title = $data['title'];
                $ticket->client_uuid = $data['client_uuid'] ?: null;
                $ticket->project_id = $data['project_id'] ?: null;
                $ticket->area_id = $data['area_id'] ?: null;
                $ticket->assigned_tech_id = $data['assigned_tech_id'] ?: null;
                $ticket->economic_weight = $data['economic_weight'] ?? $ticket->economic_weight;
                $ticket->save();

                $newStatus = $data['status'];
                if ($ticket->status::$name !== $newStatus) {
                    try {
                        $ticket->status->transitionTo($newStatus);
                    } catch (TransitionNotFound $e) {
                        Notification::make()
                            ->title('Estado no actualizado')
                            ->body('La transición de estado no está permitida por las reglas del flujo.')
                            ->warning()
                            ->send();
                    }
                }

                Notification::make()
                    ->title('Ticket actualizado')
                    ->body("El ticket fue modificado correctamente.")
                    ->success()
                    ->send();
            });
    }

    public function getColumnsProperty(): array
    {
        if ($this->viewMode === 'assignee') {
            $columns = [
                [
                    'id' => 'unassigned',
                    'title' => 'Sin Asignar',
                    'badge_color' => 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/30',
                    'card_border' => 'border-l-gray-400 dark:border-l-gray-500',
                    'match' => fn ($ticket) => is_null($ticket->assigned_tech_id),
                ],
            ];

            foreach ($this->users as $user) {
                $columns[] = [
                    'id' => (string) $user->id,
                    'title' => $user->name,
                    'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/30',
                    'card_border' => 'border-l-indigo-500 dark:border-l-indigo-400',
                    'match' => fn ($ticket) => (string) $ticket->assigned_tech_id === (string) $user->id,
                ];
            }

            return $columns;
        }

        return [
            [
                'id' => Open::$name,
                'title' => 'Abierto',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/30',
                'card_border' => 'border-l-amber-500 dark:border-l-amber-400',
                'match' => fn ($ticket) => $ticket->status::$name === Open::$name,
            ],
            [
                'id' => InProgress::$name,
                'title' => 'En Progreso',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/30',
                'card_border' => 'border-l-blue-500 dark:border-l-blue-400',
                'match' => fn ($ticket) => $ticket->status::$name === InProgress::$name,
            ],
            [
                'id' => Resolved::$name,
                'title' => 'Resuelto',
                'badge_color' => 'bg-green-100 text-green-800 border-green-300 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/30',
                'card_border' => 'border-l-green-500 dark:border-l-green-400',
                'match' => fn ($ticket) => $ticket->status::$name === Resolved::$name,
            ],
            [
                'id' => Closed::$name,
                'title' => 'Cerrado',
                'badge_color' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/30',
                'card_border' => 'border-l-purple-500 dark:border-l-purple-400',
                'match' => fn ($ticket) => $ticket->status::$name === Closed::$name,
            ],
            [
                'id' => Cancelled::$name,
                'title' => 'Cancelado',
                'badge_color' => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/30',
                'card_border' => 'border-l-red-500 dark:border-l-red-400',
                'match' => fn ($ticket) => $ticket->status::$name === Cancelled::$name,
            ],
        ];
    }

    public function getTicketsProperty()
    {
        return FsmTicket::with(['technician', 'client', 'project', 'area'])->get();
    }

    public function getFilteredTicketsProperty()
    {
        return $this->tickets->filter(function ($ticket) {
            if (empty($this->searchQuery)) {
                return true;
            }

            $search = strtolower($this->searchQuery);

            return str_contains(strtolower($ticket->title ?? ''), $search)
                || str_contains((string) $ticket->id, $search);
        });
    }

    public function getUsersProperty()
    {
        return User::role('tecnico')->orderBy('name')->get();
    }

    public function handleDrop($ticketId, $targetId): void
    {
        if ($this->viewMode === 'assignee') {
            $userId = $targetId === 'unassigned' ? null : $targetId;
            $this->updateTicketAssignee($ticketId, $userId);
        } else {
            $this->updateTicketColumn($ticketId, $targetId);
        }
    }

    public function updateTicketColumn($ticketId, $newStatusName): void
    {
        $ticket = FsmTicket::find($ticketId);

        if (! $ticket || ! $ticket->status || $ticket->status::$name === $newStatusName) {
            return;
        }

        try {
            $ticket->status->transitionTo($newStatusName);

            Notification::make()
                ->title('Estado actualizado')
                ->body("El ticket se movió correctamente.")
                ->success()
                ->send();

        } catch (TransitionNotFound $e) {
            Notification::make()
                ->title('Movimiento no permitido')
                ->body('La transición de estado no está permitida por las reglas de tu flujo de trabajo.')
                ->danger()
                ->send();
        }
    }

    public function updateTicketAssignee($ticketId, $userId): void
    {
        $ticket = FsmTicket::find($ticketId);

        if (! $ticket) {
            return;
        }

        $ticket->assigned_tech_id = $userId ?: null;
        $ticket->save();

        Notification::make()
            ->title('Asignación actualizada')
            ->body("El técnico responsable fue cambiado exitosamente.")
            ->success()
            ->send();
    }
}
