<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;

class SectionPolicy
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
    public function view(User $user, Section $section): bool
    {
        return $user->hasPermission('departments.view') && $this->withinScope($user, $section, 'departments.view');
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
    public function update(User $user, Section $section): bool
    {
        return $user->hasPermission('departments.update') && $this->withinScope($user, $section, 'departments.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Section $section): bool
    {
        return $user->hasPermission('departments.delete') && $this->withinScope($user, $section, 'departments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Section $section): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Section $section): bool
    {
        return false;
    }

    private function withinScope(User $user, Section $section, string $permission): bool
    {
        $scope = $user->highestScopeFor($permission);

        if ($scope === 'organization') {
            return true;
        }

        return $scope === 'department' && (int) $user->employee?->department_id === $section->department_id;
    }
}
