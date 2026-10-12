<?php

namespace App\Domains\Tickets\Application\Notifications\Evidence;

use App\Domains\Tickets\Application\Notifications\TicketNotification;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;

abstract class EvidenciaNotification extends TicketNotification
{
    public function __construct(
        FsmTicket $ticket,
        protected Evidence $evidence,
        protected ?User $actor = null,
    ) {
        parent::__construct($ticket);
    }

    protected function icon(): string
    {
        return 'heroicon-o-photo';
    }

    protected function withLink(): bool
    {
        return true;
    }

    protected function actor(): string
    {
        return $this->actor?->name ?? 'El sistema';
    }

    protected function archivo(): string
    {
        return basename((string) $this->evidence->file_path) ?: 'sin nombre';
    }
}
