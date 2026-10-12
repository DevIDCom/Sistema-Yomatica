<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/kanban.css') }}">

    <div class="kanban-toolbar">
        <div class="view-toggle-group">
            <button
                type="button"
                wire:click="$set('viewMode', 'status')"
                class="view-toggle-btn {{ $viewMode === 'status' ? 'active' : '' }}"
            >
                Por Estatus
            </button>

            <button
                type="button"
                wire:click="$set('viewMode', 'assignee')"
                class="view-toggle-btn {{ $viewMode === 'assignee' ? 'active' : '' }}"
            >
                Asignar a Empleado
            </button>
        </div>

        <div class="toolbar-actions">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchQuery"
                placeholder="Buscar por folio o título..."
                class="kanban-search-input"
            >

            <button
                type="button"
                wire:click="mountAction('createTicket')"
                class="kanban-btn-create"
            >
                <span>+</span> Nuevo Ticket
            </button>
        </div>
    </div>

    <div class="kanban-board-container">
        <div class="kanban-board">
            @foreach ($this->columns as $column)
                <div
                    x-data="{ isDragOver: false }"
                    x-on:dragover.prevent="isDragOver = true"
                    x-on:dragleave.prevent="isDragOver = false"
                    x-on:drop.prevent="
                        isDragOver = false;
                        let ticketId = $event.dataTransfer.getData('text/plain');
                        $wire.handleDrop(ticketId, '{{ $column['id'] }}');
                    "
                    :class="{ 'is-drag-over': isDragOver }"
                    class="kanban-col"
                >
                    <div class="kanban-header">
                        <span class="kanban-badge" style="background: rgba(0,0,0,0.04);">
                            {{ $column['title'] }}
                        </span>

                        <span class="kanban-counter">
                            {{ $this->filteredTickets->filter(fn($t) => $column['match']($t))->count() }}
                        </span>
                    </div>

                    <div class="kanban-list">
                        @foreach ($this->filteredTickets->filter(fn($t) => $column['match']($t)) as $ticket)
                            <div
                                draggable="true"
                                x-on:dragstart="
                                    $event.dataTransfer.setData('text/plain', '{{ $ticket->id }}');
                                    $event.dataTransfer.effectAllowed = 'move';
                                "
                                wire:click="mountAction('editTicket', { ticketId: {{ $ticket->id }} })"
                                class="kanban-card"
                                style="border-left-color: {{
                                    match((string) $ticket->status) {
                                        'open' => '#f59e0b',
                                        'in_progress' => '#3b82f6',
                                        'resolved', 'closed' => '#10b981',
                                        'cancelled' => '#ef4444',
                                        default => '#9ca3af',
                                    }
                                }}; cursor: pointer;"
                            >
                                <div class="card-meta">
                                    <span class="card-folio">
                                        {{ $ticket->ticket_number ?? 'S/N' }}
                                    </span>
                                    <span class="card-date">
                                        {{ $ticket->created_at->format('d/m/Y') }}
                                    </span>
                                </div>

                                <h4 class="card-title">
                                    {{ $ticket->title }}
                                </h4>

                                <!-- Stop propagation para que cambiar el select no abra el modal -->
                                <div class="card-footer" x-data @click.stop @mousedown.stop>

                                    <!-- CORRECCIÓN: assignee -> technician -->
                                    <div class="card-avatar" title="{{ $ticket->technician?->name ?? 'Sin asignar' }}">
                                        {{ $ticket->technician ? substr($ticket->technician->name, 0, 1) : '?' }}
                                    </div>

                                    <select
                                        x-on:change="$wire.updateTicketAssignee({{ $ticket->id }}, $event.target.value)"
                                        class="card-select"
                                    >
                                        <option value="">Sin asignar</option>
                                        @foreach ($this->users as $user)
                                            <!-- CORRECCIÓN: assigned_to -> assigned_tech_id -->
                                            <option
                                                value="{{ $user->id }}"
                                                @selected($ticket->assigned_tech_id == $user->id)
                                            >
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
