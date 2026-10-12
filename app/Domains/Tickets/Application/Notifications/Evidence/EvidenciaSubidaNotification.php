<?php

namespace App\Domains\Tickets\Application\Notifications\Evidence;

class EvidenciaSubidaNotification extends EvidenciaNotification
{
    protected function title(): string
    {
        return 'Nueva evidencia';
    }

    protected function body(): string
    {
        $body = "{$this->actor()} subió la evidencia «{$this->archivo()}» en {$this->label()}.";

        return $this->evidence->is_out_of_bounds
            ? $body . 'Fuera de la zona del servicio.'
            : $body;
    }

    protected function icon(): string
    {
        return 'heroicon-o-arrow-up-tray';
    }

    protected function color(): string
    {
        return $this->evidence->is_out_of_bounds ? 'warning' : 'success';
    }
}
