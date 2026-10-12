<?php

namespace App\Domains\Tickets\Domain\StateMachines\Ticket;

use App\Domains\Tickets\Domain\StateMachines\Ticket\Transitions\StartTicket;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class TicketState extends State
{
    abstract public function color(): string;
    abstract public function label(): string;
    abstract public function icon(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Open::class)
            ->allowTransition(Open::class, InProgress::class, StartTicket::class)
            ->allowTransition(InProgress::class, Resolved::class)
            ->allowTransition(Resolved::class, Closed::class)
            ->allowTransition([Open::class, InProgress::class], Cancelled::class);
    }

    public static function getSelectOptions(): array
    {
        return collect(static::all())->mapWithKeys(function (string $stateClass) {
            $stateInstance = new $stateClass(new FsmTicket());
            return [$stateClass::$name => $stateInstance->label()];
        })->toArray();
    }
}
