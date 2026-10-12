<?php

namespace App\Domains\Tickets\Application\Notifications;

class TicketEliminadoNotification extends TicketNotification
{
    protected function title(): string
    {
        return 'Ticket eliminado';
    }

    protected function body(): string
    {
        return "{$this->label()} fue eliminado.";
    }

    protected function icon(): string
    {
        return 'heroicon-o-trash';
    }

    protected function color(): string
    {
        return 'danger';
    }
}
