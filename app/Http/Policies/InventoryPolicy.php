<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoryMaster;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function update(User $user, InventoryMaster $inventoryMaster)
    {
        return $user->franchise_id == $inventoryMaster->franchise_id;
    }

    public function allocate(User $user, InventoryMaster $inventoryMaster)
    {
        // Only allow allocation if total_quantity > 0, for example
        return $user->franchise_id == $inventoryMaster->franchise_id
            && $inventoryMaster->total_quantity > 0;
    }
}
