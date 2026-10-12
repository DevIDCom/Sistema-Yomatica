<?php

namespace App\Domains\Tickets\Application\Notifications\Evidence;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;

class EvidenciaActualizadaNotification extends EvidenciaNotification
{
    private const ETIQUETAS = [
        'file_path'        => 'archivo',
        's3_key'           => 'archivo',
        'file_type'        => 'tipo',
        'location'         => 'ubicación',
        'is_out_of_bounds' => 'fuera de zona',
        'ticket_id'        => 'ticket',
        'project_id'       => 'proyecto',
        'invoice_id'       => 'factura',
        'client_id'        => 'cliente',
        'client_uuid'      => 'cliente',
    ];
    public function __construct(
        FsmTicket $ticket,
        Evidence $evidence,
        ?User $actor,
        private array $campos,
    ) {
        parent::__construct($ticket, $evidence, $actor);
    }

    protected function title(): string
    {
        return 'Evidencia modificada';
    }

    protected function body(): string
    {
        $campos = collect($this->campos)
            ->map(fn (string $campo) => self::ETIQUETAS[$campo] ?? $campo)
            ->unique()
            ->implode(', ');

        return "{$this->actor()} modificó la evidencia «{$this->archivo()}» en {$this->label()} · Cambios: {$campos}";
    }

    protected function icon(): string
    {
        return 'heroicon-o-pencil-square';
    }

    protected function color(): string
    {
        return 'warning';
    }
}
