<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /** @var array<string, string|null>|null */
    private ?array $permissionScopes = null;

    /** @var array<string, bool>|null */
    private ?array $roleMembership = null;

    protected $fillable = [
        'employee_id',
        'username',
        'email',
        'password',
        'is_active',
        'must_change_password',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function username(): string
    {
        return 'username';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'recorded_by');
    }

    public function performanceEvaluationsGiven(): HasMany
    {
        return $this->hasMany(PerformanceEvaluation::class, 'evaluator_user_id');
    }

    public function updatedSettings(): HasMany
    {
        return $this->hasMany(SystemSetting::class, 'updated_by');
    }

    public function hasRole(string $roleName): bool
    {
        $this->roleMembership ??= array_fill_keys($this->roles()->pluck('name')->all(), true);

        return isset($this->roleMembership[$roleName]);
    }

    public function roleNames(): array
    {
        return $this->roles->pluck('name')->all();
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return count(array_intersect($this->roleNames(), $roleNames)) > 0;
    }

    public function highestScopeFor(string $permissionKey): ?string
    {
        if ($this->permissionScopes === null) {
            $priority = ['organization' => 4, 'department' => 3, 'section' => 2, 'self' => 1];
            $this->permissionScopes = $this->roles()
                ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->whereNull('roles.deleted_at')
                ->whereNull('permissions.deleted_at')
                ->get(['permissions.key', 'role_permissions.scope'])
                ->filter(fn ($grant) => isset($priority[$grant->scope]))
                ->groupBy('key')
                ->map(function ($grants) use ($priority): string {
                    return $grants->sortByDesc(fn ($grant) => $priority[$grant->scope])->first()->scope;
                })
                ->all();
        }

        return $this->permissionScopes[$permissionKey] ?? null;
    }

    public function allowsEmployeeWithinScope(string $permissionKey, Employee $target): bool
    {
        $scope = $this->highestScopeFor($permissionKey);
        $ownEmployee = $this->employee;

        return match ($scope) {
            'organization' => true,
            'self' => $ownEmployee !== null && $ownEmployee->is($target),
            'department' => $ownEmployee !== null
                && (int) $ownEmployee->department_id === (int) $target->department_id,
            'section' => $ownEmployee !== null
                && $ownEmployee->section_id !== null
                && (int) $ownEmployee->department_id === (int) $target->department_id
                && (int) $ownEmployee->section_id === (int) $target->section_id,
            default => false,
        };
    }

    public function allowsDepartmentWithinScope(string $permissionKey, Department $department): bool
    {
        return match ($this->highestScopeFor($permissionKey)) {
            'organization' => true,
            'department' => $this->employee !== null
                && (int) $this->employee->department_id === (int) $department->id,
            default => false,
        };
    }

    public function allowsSectionWithinScope(string $permissionKey, Section $section): bool
    {
        $scope = $this->highestScopeFor($permissionKey);
        $employee = $this->employee;

        return match ($scope) {
            'organization' => true,
            'department' => $employee !== null
                && (int) $employee->department_id === (int) $section->department_id,
            'section' => $employee !== null
                && (int) $employee->department_id === (int) $section->department_id
                && (int) $employee->section_id === (int) $section->id,
            default => false,
        };
    }

    public function employeesWithinScope(string $permissionKey, ?string $scopeOverride = null): Builder
    {
        $query = Employee::query();
        $scope = $scopeOverride ?? $this->highestScopeFor($permissionKey);
        $ownEmployee = $this->employee;

        if ($scope === 'organization') {
            return $query;
        }

        if ($ownEmployee === null) {
            return $query->whereRaw('1 = 0');
        }

        return match ($scope) {
            'self' => $query->whereKey($ownEmployee->id),
            'department' => $query->where('department_id', $ownEmployee->department_id),
            'section' => $ownEmployee->section_id === null
                ? $query->whereRaw('1 = 0')
                : $query->where('department_id', $ownEmployee->department_id)
                    ->where('section_id', $ownEmployee->section_id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public function projectsWithinScope(string $permissionKey, ?string $scopeOverride = null): Builder
    {
        $query = Project::query();
        $scope = $scopeOverride ?? $this->highestScopeFor($permissionKey);

        if ($scope === 'organization') {
            return $query;
        }

        if ($scope === 'department') {
            return $this->employee === null
                ? $query->whereRaw('1 = 0')
                : $query->where('department_id', $this->employee->department_id);
        }

        if ($scope === 'section') {
            $employeeIds = $this->employeesWithinScope($permissionKey, $scope)->select('employees.id');

            return $query->where(fn (Builder $projects) => $projects
                ->whereIn('owner_employee_id', $employeeIds)
                ->orWhereHas('members', fn (Builder $members) => $members->whereIn('employees.id', $employeeIds)));
        }

        if ($scope === 'self' && $this->employee !== null) {
            $employeeId = $this->employee->id;

            return $query->where(fn (Builder $projects) => $projects
                ->where('owner_employee_id', $employeeId)
                ->orWhereHas('members', fn (Builder $members) => $members->where('employees.id', $employeeId)));
        }

        return $query->whereRaw('1 = 0');
    }

    public function allowsProjectWithinScope(string $permissionKey, Project $project): bool
    {
        $scope = $this->highestScopeFor($permissionKey);
        $employee = $this->employee;

        return match ($scope) {
            'organization' => true,
            'department' => $employee !== null && (int) $employee->department_id === (int) $project->department_id,
            'section' => $this->projectsWithinScope($permissionKey)->whereKey($project->id)->exists(),
            'self' => $employee !== null && ((int) $project->owner_employee_id === (int) $employee->id
                || ($permissionKey === 'projects.view'
                    && $project->members()->where('employees.id', $employee->id)->exists())),
            default => false,
        };
    }

    public function tasksWithinScope(string $permissionKey, ?string $scopeOverride = null): Builder
    {
        $employeeIds = $this->employeesWithinScope($permissionKey, $scopeOverride)->select('employees.id');

        return Task::query()->whereIn('assignee_employee_id', $employeeIds);
    }

    public function allowsTaskWithinScope(string $permissionKey, Task $task): bool
    {
        return $task->assignee !== null
            && $this->allowsEmployeeWithinScope($permissionKey, $task->assignee);
    }

    public function usersWithinScope(string $permissionKey): Builder
    {
        $scope = $this->highestScopeFor($permissionKey);
        $query = User::query();

        if ($scope === 'organization') {
            return $query;
        }

        if ($scope === 'self') {
            return $query->whereKey($this->id);
        }

        return $query->whereIn('employee_id', $this->employeesWithinScope($permissionKey)->select('employees.id'));
    }

    public function allowsUserWithinScope(string $permissionKey, User $target): bool
    {
        if ($this->highestScopeFor($permissionKey) === 'self') {
            return $this->is($target);
        }

        return $target->employee !== null
            && $this->allowsEmployeeWithinScope($permissionKey, $target->employee);
    }

    public function forgetAuthorizationCache(): void
    {
        $this->permissionScopes = null;
        $this->roleMembership = null;
        $this->unsetRelation('roles');
    }

    public function hasPermission(string $permissionKey): bool
    {
        return $this->highestScopeFor($permissionKey) !== null;
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }
}
