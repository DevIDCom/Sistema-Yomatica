<?php

declare(strict_types=1);

namespace App\Domains\Proyectos\Domain\Policies;

use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class FsmProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FsmProject');
    }

    public function view(AuthUser $authUser, FsmProject $fsmProject): bool
    {
        return $authUser->can('View:FsmProject');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FsmProject');
    }

    public function update(AuthUser $authUser, FsmProject $fsmProject): bool
    {
        return $authUser->can('Update:FsmProject');
    }

    public function delete(AuthUser $authUser, FsmProject $fsmProject): bool
    {
        return $authUser->can('Delete:FsmProject');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FsmProject');
    }

    public function restore(AuthUser $authUser, FsmProject $fsmProject): bool
    {
        return $authUser->can('Restore:FsmProject');
    }

    public function forceDelete(AuthUser $authUser, FsmProject $fsmProject): bool
    {
        return $authUser->can('ForceDelete:FsmProject');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FsmProject');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FsmProject');
    }

    public function replicate(AuthUser $authUser, FsmProject $fsmProject): bool
    {
        return $authUser->can('Replicate:FsmProject');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FsmProject');
    }

}
