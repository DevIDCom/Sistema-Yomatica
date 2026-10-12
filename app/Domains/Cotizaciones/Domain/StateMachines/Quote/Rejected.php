<?php

namespace App\Domains\Cotizaciones\Domain\StateMachines\Quote;

class Rejected extends QuoteState
{
    public static string $name = 'rejected';

    public function label(): string { return 'Rechazada'; }
    public function color(): string { return 'danger'; }
    public function icon(): string { return 'heroicon-o-x-circle'; }
}
