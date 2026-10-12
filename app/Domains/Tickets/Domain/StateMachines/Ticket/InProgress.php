<?php

namespace App\Domains\Tickets\Domain\StateMachines\Ticket;

class InProgress extends TicketState
{
    public static $name = 'in_progress';

    public function color(): string
    {
        return 'info';
    }

    public function label(): string
    {
        return 'En Progreso';
    }

    public function icon(): string
    {
        return 'heroicon-o-play';
    }
}
