<?php

namespace App\Domains\Cotizaciones\Domain\StateMachines\Quote;

class Draft extends QuoteState
{
    public static string $name = 'draft';

    public function label(): string { return 'Borrador'; }
    public function color(): string { return 'gray'; }
    public function icon(): string { return 'heroicon-o-pencil-square'; }
}
