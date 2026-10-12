<?php

namespace App\Domains\Tickets\Domain\StateMachines\Ticket;

class Open extends TicketState
{
    public static $name = 'open';

    public function color(): string
    {
        return 'warning';
    }

    public function label(): string
    {
        return 'Abierto';
    }

    public function icon(): string
    {
        return 'heroicon-o-document';
    }
}
