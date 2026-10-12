<?php

namespace App\Domains\Cotizaciones\Domain\StateMachines\Quote;

class Expired extends QuoteState
{
    public static string $name = 'expired';

    public function label(): string { return 'Expirada'; }
    public function color(): string { return 'warning'; }
    public function icon(): string { return 'heroicon-o-clock'; }
}
