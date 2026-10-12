<?php

namespace App\Domains\Tickets\Application\Notifications\Evidence;

class EvidenciaEliminadaPorAdminNotification extends EvidenciaNotification
{
    protected function title(): string
    {
        return 'Un administrador eliminó tu evidencia';
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
