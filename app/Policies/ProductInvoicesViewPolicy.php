<?php

namespace App\Policies;

use App\Models\User;
use App\Models\product_invoices_view;
use Illuminate\Auth\Access\Response;

class ProductInvoicesViewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, product_invoices_view $productInvoicesView): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, product_invoices_view $productInvoicesView): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, product_invoices_view $productInvoicesView): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, product_invoices_view $productInvoicesView): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, product_invoices_view $productInvoicesView): bool
    {
        //
    }
}
