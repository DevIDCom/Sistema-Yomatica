<?php

namespace App\Domains\Tickets\Application\Notifications\Evidence;

class EvidenciaEliminadaNotification extends EvidenciaNotification
{
    protected function title(): string
    {
        return 'Evidencia eliminada';
    }

    protected function body(): string
    {
        return "{$this->actor()} eliminó la evidencia «{$this->archivo()}» de {$this->label()}.";
    }

    protected function icon(): string
    {
        return 'heroicon-o-trash';
    }

    protected function color(): string
    {
        return 'danger';
    }
}
