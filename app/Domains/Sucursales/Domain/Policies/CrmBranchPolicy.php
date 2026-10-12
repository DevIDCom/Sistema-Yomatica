<?php

declare(strict_types=1);

namespace App\Domains\Sucursales\Domain\Policies;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CrmBranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CrmBranch');
    }

    public function view(AuthUser $authUser, CrmBranch $crmBranch): bool
    {
        return $authUser->can('View:CrmBranch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CrmBranch');
    }

    public function update(AuthUser $authUser, CrmBranch $crmBranch): bool
    {
        return $authUser->can('Update:CrmBranch');
    }

    public function delete(AuthUser $authUser, CrmBranch $crmBranch): bool
    {
        return $authUser->can('Delete:CrmBranch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CrmBranch');
    }

    public function restore(AuthUser $authUser, CrmBranch $crmBranch): bool
    {
        return $authUser->can('Restore:CrmBranch');
    }

    public function forceDelete(AuthUser $authUser, CrmBranch $crmBranch): bool
    {
        return $authUser->can('ForceDelete:CrmBranch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CrmBranch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CrmBranch');
    }

    public function replicate(AuthUser $authUser, CrmBranch $crmBranch): bool
    {
        return $authUser->can('Replicate:CrmBranch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CrmBranch');
    }

}
