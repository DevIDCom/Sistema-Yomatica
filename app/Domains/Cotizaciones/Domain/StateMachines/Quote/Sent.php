<?php

namespace App\Domains\Cotizaciones\Domain\StateMachines\Quote;

class Sent extends QuoteState
{
    public static string $name = 'sent';

    public function label(): string { return 'Enviada'; }
    public function color(): string { return 'info'; }
    public function icon(): string { return 'heroicon-o-paper-airplane'; }
}
