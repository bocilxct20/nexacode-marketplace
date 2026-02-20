<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Determine if the product can be updated by the user.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->author_id;
    }

    /**
     * Determine if the product can be deleted by the user.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->author_id || $user->isAdmin();
    }

    /**
     * Determine if the user can manage versions for the product.
     */
    public function manageVersion(User $user, Product $product): bool
    {
        return $user->id === $product->author_id;
    }
}
