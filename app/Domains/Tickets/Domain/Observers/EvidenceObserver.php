<?php

namespace App\Domains\Tickets\Domain\Observers;

use App\Domains\Tickets\Application\Notifications\Evidence\EvidenciaActualizadaNotification;
use App\Domains\Tickets\Application\Notifications\Evidence\EvidenciaEliminadaNotification;
use App\Domains\Tickets\Application\Notifications\Evidence\EvidenciaEliminadaPorAdminNotification;
use App\Domains\Tickets\Application\Notifications\Evidence\EvidenciaNotification;
use App\Domains\Tickets\Application\Notifications\Evidence\EvidenciaSubidaNotification;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;
use App\Domains\Users\Models\User;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Auth;
use Throwable;

class EvidenceObserver implements ShouldHandleEventsAfterCommit
{
    private const ROL_ADMIN = 'super_admin';

    public function created(Evidence $evidence): void
    {
        if (! $ticket = $evidence->ticket) {
            return;
        }

        $actor = $this->actor();

        $this->notificarAdmins(
            new EvidenciaSubidaNotification($ticket, $evidence, $actor),
            $actor,
        );
    }

    public function updated(Evidence $evidence): void
    {
        $campos = array_keys($evidence->getChanges());

        if (empty($campos) || ! $ticket = $evidence->ticket) {
            return;
        }

        $actor = $this->actor();

        $this->notificarAdmins(
            new EvidenciaActualizadaNotification($ticket, $evidence, $actor, $campos),
            $actor,
        );
    }

    public function deleted(Evidence $evidence): void
    {
        if (! $ticket = $evidence->ticket) {
            return;
        }

        $actor = $this->actor();

        if ($actor && $this->esAdmin($actor)) {
            try {
                $tecnico = $ticket->technician;

                if ($tecnico && $tecnico->isNot($actor)) {
                    (new EvidenciaEliminadaPorAdminNotification($ticket, $evidence, $actor))
                        ->sendTo($tecnico);
                }
            } catch (Throwable $e) {
                report($e);
            }

            return;
        }

        $this->notificarAdmins(
            new EvidenciaEliminadaNotification($ticket, $evidence, $actor),
            $actor,
        );
    }

    private function notificarAdmins(EvidenciaNotification $notification, ?User $actor): void
    {
        try {
            User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', self::ROL_ADMIN))
                ->when($actor, fn ($q) => $q->whereKeyNot($actor->getKey()))
                ->each(fn (User $admin) => $notification->sendTo($admin));
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function esAdmin(User $user): bool
    {
        return $user->hasRole(self::ROL_ADMIN);
    }

    private function actor(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }
}
