<?php

declare(strict_types=1);

namespace App\Domains\Tickets\Domain\Policies;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class FsmTicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FsmTicket');
    }

    public function view(AuthUser $authUser, FsmTicket $fsmTicket): bool
    {
        if ($authUser->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }
        return $fsmTicket->assigned_tech_id === $authUser->id;
    }

    public function update(AuthUser $authUser, FsmTicket $fsmTicket): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin']);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FsmTicket');
    }

    public function delete(AuthUser $authUser, FsmTicket $fsmTicket): bool
    {
        return $authUser->can('Delete:FsmTicket');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FsmTicket');
    }

    public function restore(AuthUser $authUser, FsmTicket $fsmTicket): bool
    {
        return $authUser->can('Restore:FsmTicket');
    }

    public function forceDelete(AuthUser $authUser, FsmTicket $fsmTicket): bool
    {
        return $authUser->can('ForceDelete:FsmTicket');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FsmTicket');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FsmTicket');
    }

    public function replicate(AuthUser $authUser, FsmTicket $fsmTicket): bool
    {
        return $authUser->can('Replicate:FsmTicket');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FsmTicket');
    }
}
