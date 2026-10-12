<?php

namespace App\Domains\Tickets\Domain\StateMachines\Ticket;

class Cancelled extends TicketState
{
    public static $name = 'cancelled';

    public function color(): string
    {
        return 'danger';
    }

    public function label(): string
    {
        return 'Cancelado';
    }

    public function icon(): string
    {
        return 'heroicon-o-x-circle';
    }
}
