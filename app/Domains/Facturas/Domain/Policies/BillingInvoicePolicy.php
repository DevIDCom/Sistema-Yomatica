<?php

declare(strict_types=1);

namespace App\Domains\Facturas\Domain\Policies;

use App\Domains\Facturas\Infraestructure\Enloquet\Models\BillingInvoice;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BillingInvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BillingInvoice');
    }

    public function view(AuthUser $authUser, BillingInvoice $billingInvoice): bool
    {
        return $authUser->can('View:BillingInvoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BillingInvoice');
    }

    public function update(AuthUser $authUser, BillingInvoice $billingInvoice): bool
    {
        return $authUser->can('Update:BillingInvoice');
    }

    public function delete(AuthUser $authUser, BillingInvoice $billingInvoice): bool
    {
        return $authUser->can('Delete:BillingInvoice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BillingInvoice');
    }

    public function restore(AuthUser $authUser, BillingInvoice $billingInvoice): bool
    {
        return $authUser->can('Restore:BillingInvoice');
    }

    public function forceDelete(AuthUser $authUser, BillingInvoice $billingInvoice): bool
    {
        return $authUser->can('ForceDelete:BillingInvoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BillingInvoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BillingInvoice');
    }

    public function replicate(AuthUser $authUser, BillingInvoice $billingInvoice): bool
    {
        return $authUser->can('Replicate:BillingInvoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BillingInvoice');
    }

}
