<?php

namespace App\Domains\Tickets\Application\Notifications;

class NuevoTicketNotification extends TicketNotification
{
    protected function title(): string
    {
        return 'Nuevo ticket asignado';
    }

    protected function body(): string
    {
        $prioridad = match ($this->ticket->priority) {
            'low'    => 'Baja',
            'medium' => 'Media',
            'high'   => 'Alta',
            'urgent' => 'Urgente',
            default  => $this->ticket->priority,
        };

        return "Se te asignó {$this->label()} · Prioridad: {$prioridad}";
    }

    protected function icon(): string
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    protected function color(): string
    {
        return $this->ticket->priority === 'urgent' ? 'danger' : 'info';
    }

    protected function withLink(): bool
    {
        return true;
    }
}
