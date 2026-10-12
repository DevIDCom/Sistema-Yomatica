<?php

namespace App\Domains\Tickets\Application\Notifications;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;

class TicketRetiradoNotification extends TicketNotification
{
    public function __construct(
        FsmTicket $ticket,
        private bool $reasignado,
    ) {
        parent::__construct($ticket);
    }

    protected function title(): string
    {
        return 'Ticket retirado';
    }

    protected function body(): string
    {
        return $this->reasignado
            ? "{$this->label()} fue reasignado a otro técnico."
            : "{$this->label()} quedó sin técnico asignado.";
    }

    protected function icon(): string
    {
        return 'heroicon-o-user-minus';
    }

    protected function color(): string
    {
        return 'warning';
    }
}
