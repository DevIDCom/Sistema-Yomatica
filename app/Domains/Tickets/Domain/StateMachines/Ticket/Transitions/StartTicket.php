<?php

namespace App\Domains\Tickets\Domain\StateMachines\Ticket\Transitions;

use App\Domains\Tickets\Domain\StateMachines\Ticket\InProgress;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Spatie\ModelStates\Transition;

class StartTicket extends Transition
{
    private FsmTicket $ticket;

    public function __construct(FsmTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function handle(): FsmTicket
    {
        $this->ticket->status = new InProgress($this->ticket);

        $this->ticket->started_at = now();
        $this->ticket->started_by = auth()->id();

        $this->ticket->save();

        if ($this->ticket->client?->email) {
            // Notification::route('mail', $this->ticket->client->email)
            //     ->notify(new \App\Notifications\TicketStartedNotification($this->ticket));
        }

        return $this->ticket;
    }
}
