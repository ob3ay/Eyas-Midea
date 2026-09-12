<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'username',
        'email',
        'password',
        'is_active',
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
        return $this->roles()->where('name', $roleName)->exists();
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
        $scopes = $this->roles()
            ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('permissions.key', $permissionKey)
            ->pluck('role_permissions.scope')
            ->all();

        $priority = ['organization' => 3, 'department' => 2, 'self' => 1];

        if (empty($scopes)) {
            return null;
        }

        usort($scopes, fn($a, $b) => $priority[$b] <=> $priority[$a]);

        return $scopes[0];
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
