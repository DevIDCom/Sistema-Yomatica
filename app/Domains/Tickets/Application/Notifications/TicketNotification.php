<?php

namespace App\Domains\Tickets\Application\Notifications;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\TicketResource;
use App\Domains\Users\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

abstract class TicketNotification
{
    public function __construct(protected FsmTicket $ticket) {}

    abstract protected function title(): string;

    abstract protected function body(): string;

    protected function icon(): string
    {
        return 'heroicon-o-bell';
    }

    protected function color(): string
    {
        return 'info';
    }

    protected function withLink(): bool
    {
        return false;
    }

    public function sendTo(User $user): void
    {
        $notification = Notification::make()
            ->title($this->title())
            ->body($this->body())
            ->icon($this->icon())
            ->color($this->color());

        if ($this->withLink()) {
            $notification->actions([
                Action::make('ver')
                    ->label('Ver ticket')
                    ->url(TicketResource::getUrl('edit', ['record' => $this->ticket], isAbsolute: false))
                    ->markAsRead(),
            ]);
        }

        $notification->sendToDatabase($user, isEventDispatched: true);

        $notification->broadcast($user);
    }

    protected function label(): string
    {
        return ($this->ticket->code ? "[{$this->ticket->code}] " : '') . $this->ticket->title;
    }
}
