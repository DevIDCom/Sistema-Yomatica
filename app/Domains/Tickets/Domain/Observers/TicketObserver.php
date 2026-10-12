<?php

namespace App\Domains\Tickets\Domain\Observers;

use App\Domains\Tickets\Application\Notifications\NuevoTicketNotification;
use App\Domains\Tickets\Application\Notifications\TicketAsignadoNotification;
use App\Domains\Tickets\Application\Notifications\TicketEliminadoNotification;
use App\Domains\Tickets\Application\Notifications\TicketNotification;
use App\Domains\Tickets\Application\Notifications\TicketRetiradoNotification;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class TicketObserver implements ShouldHandleEventsAfterCommit
{
    public function created(FsmTicket $ticket): void
    {
        $this->send($ticket->assigned_tech_id, new NuevoTicketNotification($ticket));
    }

    public function updated(FsmTicket $ticket): void
    {
        if (! $ticket->wasChanged('assigned_tech_id')) {
            return;
        }

        $anterior = $ticket->getPrevious()['assigned_tech_id'] ?? null;
        $nuevo = $ticket->assigned_tech_id;

        $this->send($anterior, new TicketRetiradoNotification($ticket, reasignado: (bool) $nuevo));
        $this->send($nuevo, new TicketAsignadoNotification($ticket));
    }

    public function deleted(FsmTicket $ticket): void
    {
        if (! $ticket->exists) {
            return;
        }

        $this->send($ticket->assigned_tech_id, new TicketEliminadoNotification($ticket));
    }

    private function send(mixed $techId, TicketNotification $notification): void
    {
        $techId = $techId ? (int) $techId : null;

        if (! $techId || $techId === (int) auth()->id()) {
            return;
        }

        if ($tech = User::find($techId)) {
            $notification->sendTo($tech);
        }
    }
}
