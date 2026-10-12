<?php
namespace App\Domains\Cotizaciones\Domain\StateMachines\Quote;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class QuoteState extends State
{
    abstract public function label(): string;

    abstract public function color(): string;

    abstract public function icon(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Sent::class)
            ->allowTransition(Draft::class, Expired::class)
            ->allowTransition(Sent::class, Approved::class)
            ->allowTransition(Sent::class, Rejected::class)
            ->allowTransition(Sent::class, Expired::class)
            ->allowTransition(Sent::class, Draft::class)
            ->allowTransition(Rejected::class, Draft::class)
            ->allowTransition(Expired::class, Draft::class);
    }

    public static function getSelectOptions(): array
    {
        return [
            Draft::$name => 'Borrador',
            Sent::$name => 'Enviada',
            Approved::$name => 'Aprobada',
            Rejected::$name => 'Rechazada',
            Expired::$name => 'Expirada',
        ];
    }
}
