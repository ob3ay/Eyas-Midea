<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('departments.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $department): bool
    {
        return $user->hasPermission('departments.view') && $this->withinScope($user, $department, 'departments.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('departments.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Department $department): bool
    {
        return $user->hasPermission('departments.update') && $this->withinScope($user, $department, 'departments.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $department): bool
    {
        return $user->hasPermission('departments.delete') && $this->withinScope($user, $department, 'departments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Department $department): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        return false;
    }

    private function withinScope(User $user, Department $department, string $permission): bool
    {
        $scope = $user->highestScopeFor($permission);

        if ($scope === 'organization') {
            return true;
        }

        return $scope === 'department' && (int) $user->employee?->department_id === $department->id;
    }
}
