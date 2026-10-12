<?php

namespace App\Domains\Tickets\Domain\StateMachines\Ticket;

class Resolved extends TicketState
{
    public static $name = 'resolved';

    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Resuelto';
    }

    public function icon(): string
    {
        return 'heroicon-o-check-circle';
    }
}
