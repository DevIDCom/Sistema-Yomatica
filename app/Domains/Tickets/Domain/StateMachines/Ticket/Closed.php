<?php
namespace App\Domains\Tickets\Domain\StateMachines\Ticket;

class Closed extends TicketState
{
    public static $name = 'closed';

    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Cerrado';
    }

    public function icon(): string
    {
        return 'heroicon-o-lock-closed';
    }
}
