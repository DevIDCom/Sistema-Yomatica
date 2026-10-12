<?php

declare(strict_types=1);

namespace App\Domains\Productos\Domain\Policies;

use App\Domains\Productos\Infraestructure\Eloquent\Models\BillingInvoiceItem;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BillingInvoiceItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BillingInvoiceItem');
    }

    public function view(AuthUser $authUser, BillingInvoiceItem $billingInvoiceItem): bool
    {
        return $authUser->can('View:BillingInvoiceItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BillingInvoiceItem');
    }

    public function update(AuthUser $authUser, BillingInvoiceItem $billingInvoiceItem): bool
    {
        return $authUser->can('Update:BillingInvoiceItem');
    }

    public function delete(AuthUser $authUser, BillingInvoiceItem $billingInvoiceItem): bool
    {
        return $authUser->can('Delete:BillingInvoiceItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BillingInvoiceItem');
    }

    public function restore(AuthUser $authUser, BillingInvoiceItem $billingInvoiceItem): bool
    {
        return $authUser->can('Restore:BillingInvoiceItem');
    }

    public function forceDelete(AuthUser $authUser, BillingInvoiceItem $billingInvoiceItem): bool
    {
        return $authUser->can('ForceDelete:BillingInvoiceItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BillingInvoiceItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BillingInvoiceItem');
    }

    public function replicate(AuthUser $authUser, BillingInvoiceItem $billingInvoiceItem): bool
    {
        return $authUser->can('Replicate:BillingInvoiceItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BillingInvoiceItem');
    }

}
