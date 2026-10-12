<?php

declare(strict_types=1);

namespace App\Domains\Cotizaciones\Domain\Policies;

use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CpqQuotePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CpqQuote');
    }

    public function view(AuthUser $authUser, CpqQuote $cpqQuote): bool
    {
        return $authUser->can('View:CpqQuote');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CpqQuote');
    }

    public function update(AuthUser $authUser, CpqQuote $cpqQuote): bool
    {
        return $authUser->can('Update:CpqQuote');
    }

    public function delete(AuthUser $authUser, CpqQuote $cpqQuote): bool
    {
        return $authUser->can('Delete:CpqQuote');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CpqQuote');
    }

    public function restore(AuthUser $authUser, CpqQuote $cpqQuote): bool
    {
        return $authUser->can('Restore:CpqQuote');
    }

    public function forceDelete(AuthUser $authUser, CpqQuote $cpqQuote): bool
    {
        return $authUser->can('ForceDelete:CpqQuote');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CpqQuote');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CpqQuote');
    }

    public function replicate(AuthUser $authUser, CpqQuote $cpqQuote): bool
    {
        return $authUser->can('Replicate:CpqQuote');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CpqQuote');
    }

}
