<?php

namespace App\Domains\Cotizaciones\Domain\StateMachines\Quote;

class Approved extends QuoteState
{
    public static string $name = 'approved';

    public function label(): string { return 'Aprobada'; }
    public function color(): string { return 'success'; }
    public function icon(): string { return 'heroicon-o-check-circle'; }
}
