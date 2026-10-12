<?php

namespace App\Domains\Tickets\Application\Notifications;

class TicketAsignadoNotification extends TicketNotification
{
    protected function title(): string
    {
        return 'Ticket asignado';
    }

    protected function body(): string
    {
        return "Se te asignó {$this->label()}.";
    }

    protected function icon(): string
    {
        return 'heroicon-o-user-plus';
    }

    protected function withLink(): bool
    {
        return true;
    }
}
