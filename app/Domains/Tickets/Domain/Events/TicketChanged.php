<?php

namespace App\Domains\Tickets\Domain\Events;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class TicketChanged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public int $ticketId,
        public ?int $assignedTechId = null,
        public string $action = 'updated',
    ) {}

    public static function from(FsmTicket $ticket, string $action = 'updated'): self
    {
        return new self($ticket->id, $ticket->assigned_tech_id, $action);
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('tickets')];
    }

    public function broadcastAs(): string
    {
        return 'TicketChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticketId,
            'assigned_tech_id' => $this->assignedTechId,
            'action' => $this->action,
        ];
    }
}
