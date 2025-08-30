<?php

namespace Vgplay\Admins\Policies;

use Vgplay\Admins\Models\Role;
use Vgplay\Admins\Models\Admin;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Admin', 'Dev']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        return $admin->hasRole(['Admin', 'Dev']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, Role $role): bool
    {
        return $admin->hasRole(['Admin', 'Dev']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, Role $role): bool
    {
        return $admin->hasRole(['Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Admin $admin, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Role $role): bool
    {
        return false;
    }
}
