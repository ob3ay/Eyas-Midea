<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('users.view');

        $users = $this->scopedUsersQuery($user, $scope)
            ->with(['employee.department', 'employee.section', 'roles.permissions'])
            ->orderByDesc('is_active')
            ->orderBy('username')
            ->get();

        $roles = Role::with('permissions')->orderBy('display_name')->get();
        $permissions = Permission::orderBy('display_name')->get();
        $employees = $user->employeesWithinScope('users.create')
            ->whereDoesntHave('user')
            ->orderBy('full_name')
            ->get();

        $superAdmin = $users->first(function ($u) {
            return $u->roles->contains(fn ($role) => $role->name === 'SA');
        });

        $canCreate = $user->hasPermission('users.create');
        $canUpdate = $user->hasPermission('users.update');
        $canDelete = $user->hasPermission('users.delete');
        $canManagePermissions = $user->hasPermission('settings.manage_permissions');

        return view('users.index', compact(
            'users',
            'roles',
            'permissions',
            'employees',
            'superAdmin',
            'canCreate',
            'canUpdate',
            'canDelete',
            'canManagePermissions',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('users.create');

        $data = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->whereNull('deleted_at'), 'unique:users,employee_id'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::exists('roles', 'id')->whereNull('deleted_at')],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        abort_unless($user->allowsEmployeeWithinScope('users.create', $employee), 403, 'لا يمكنك إدارة حساب موظف خارج نطاقك.');
        $this->assertAssignableRoles($user, $data['roles'] ?? []);

        DB::transaction(function () use ($data, $request, $user): void {
            $newUser = User::create([
                'employee_id' => $data['employee_id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => $request->boolean('is_active'),
                'must_change_password' => true,
            ]);

            if ($user->hasPermission('settings.manage_permissions')) {
                $newUser->roles()->sync($data['roles'] ?? []);
            }
            AuditLog::record('Create', 'User', $newUser->id);
        });

        return back()->with('success', 'تم إنشاء حساب المستخدم بنجاح');
    }

    public function update(Request $request, User $targetUser): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('users.update', $targetUser, $user);

        if ($targetUser->hasRole('SA') && ! $user->hasRole('SA')) {
            abort(403, 'لا يمكنك تعديل حساب Super Admin.');
        }

        $data = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->whereNull('deleted_at'), Rule::unique('users', 'employee_id')->ignore($targetUser->id)],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($targetUser->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::exists('roles', 'id')->whereNull('deleted_at')],
        ]);

        if ($targetUser->hasRole('SA') && $this->isLastActiveSuperAdmin($targetUser)) {
            $saRoleId = Role::where('name', 'SA')->value('id');
            $removesSuperAdmin = array_key_exists('roles', $data)
                && ! in_array((int) $saRoleId, array_map('intval', $data['roles'] ?? []), true);
            if ($removesSuperAdmin || ! $request->boolean('is_active')) {
                abort(403, 'لا يمكن تعطيل أو إزالة دور آخر مسؤول نظام نشط.');
            }
        }

        $employee = Employee::findOrFail($data['employee_id']);
        abort_unless($user->allowsEmployeeWithinScope('users.update', $employee), 403, 'لا يمكنك إدارة حساب موظف خارج نطاقك.');
        $this->assertAssignableRoles($user, $data['roles'] ?? []);

        DB::transaction(function () use ($targetUser, $user, $data, $request): void {
            $saRole = $this->lockSuperAdminRole();
            $targetUser->refresh();
            $targetUser->forgetAuthorizationCache();
            if ($saRole !== null && $targetUser->hasRole('SA') && $this->isLastActiveSuperAdmin($targetUser)) {
                $removesSuperAdmin = array_key_exists('roles', $data)
                    && ! in_array((int) $saRole->id, array_map('intval', $data['roles'] ?? []), true);
                if ($removesSuperAdmin || ! $request->boolean('is_active')) {
                    abort(403, 'لا يمكن تعطيل أو إزالة دور آخر مسؤول نظام نشط.');
                }
            }

            $targetUser->employee_id = $data['employee_id'];
            $targetUser->username = $data['username'];
            $targetUser->email = $data['email'];
            $targetUser->is_active = $request->boolean('is_active');
            if (! empty($data['password'])) {
                $targetUser->password = $data['password'];
                $targetUser->must_change_password = true;
            }

            $targetUser->save();

            if ($user->hasPermission('settings.manage_permissions') && array_key_exists('roles', $data)) {
                $targetUser->roles()->sync($data['roles'] ?? []);
                $targetUser->forgetAuthorizationCache();
            }
            AuditLog::record('Update', 'User', $targetUser->id);
        });

        return back()->with('success', 'تم تحديث حساب المستخدم بنجاح');
    }

    public function updateRole(Request $request, User $targetUser): JsonResponse
    {
        $user = $request->user();
        $this->authorizeScope('users.update', $targetUser, $user);

        if (! $user->hasPermission('settings.manage_permissions')) {
            abort(403, 'لا تملك صلاحية إدارة أدوار المستخدمين.');
        }

        if ($targetUser->hasRole('SA') && ! $user->hasRole('SA')) {
            abort(403, 'لا يمكنك تعديل دور Super Admin.');
        }

        $data = $request->validate([
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->whereNull('deleted_at')],
        ]);

        $this->assertAssignableRoles($user, [$data['role_id']]);

        $role = Role::findOrFail($data['role_id']);
        if ($targetUser->hasRole('SA') && $this->isLastActiveSuperAdmin($targetUser) && $role->name !== 'SA') {
            abort(403, 'لا يمكن إزالة دور آخر مسؤول نظام نشط.');
        }
        DB::transaction(function () use ($targetUser, $role): void {
            $saRole = $this->lockSuperAdminRole();
            $targetUser->refresh();
            $targetUser->forgetAuthorizationCache();
            if ($saRole !== null && $targetUser->hasRole('SA') && $this->isLastActiveSuperAdmin($targetUser) && $role->name !== 'SA') {
                abort(403, 'لا يمكن إزالة دور آخر مسؤول نظام نشط.');
            }

            $targetUser->roles()->sync([$role->id]);
            $targetUser->forgetAuthorizationCache();
            AuditLog::record('UpdateRole', 'User', $targetUser->id);
        });

        return response()->json([
            'message' => 'تم تحديث دور المستخدم بنجاح.',
            'role' => [
                'id' => $role->id,
                'name' => $role->display_name ?: $role->name,
            ],
        ]);
    }

    public function destroy(Request $request, User $targetUser): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('users.delete', $targetUser, $user);

        if ($targetUser->id === $user->id) {
            return back()->with('error', 'لا يمكن حذف الحساب الحالي أثناء تسجيل الدخول به.');
        }

        if ($targetUser->hasRole('SA') && ! $user->hasRole('SA')) {
            abort(403, 'لا يمكنك حذف حساب Super Admin.');
        }

        if ($targetUser->hasRole('SA') && $this->isLastActiveSuperAdmin($targetUser)) {
            abort(403, 'لا يمكن حذف آخر مسؤول نظام نشط.');
        }

        DB::transaction(function () use ($targetUser): void {
            $this->lockSuperAdminRole();
            $targetUser->refresh();
            $targetUser->forgetAuthorizationCache();
            if ($targetUser->hasRole('SA') && $this->isLastActiveSuperAdmin($targetUser)) {
                abort(403, 'لا يمكن حذف آخر مسؤول نظام نشط.');
            }

            $targetUser->delete();
            AuditLog::record('Delete', 'User', $targetUser->id);
        });

        return back()->with('success', 'تم حذف حساب المستخدم بنجاح');
    }

    private function authorizeScope(string $permissionKey, ?User $target = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        if ($target !== null && ! $user->allowsUserWithinScope($permissionKey, $target)) {
            abort(403, 'غير مصرح لك بإدارة هذا الحساب خارج نطاقك.');
        }

        return $scope;
    }

    private function scopedUsersQuery(User $user, string $scope)
    {
        return $user->usersWithinScope('users.view');
    }

    private function assertAssignableRoles(User $user, array $roleIds): void
    {
        if (! $user->hasPermission('settings.manage_permissions') || $user->hasRole('SA')) {
            return;
        }

        $roles = Role::with('permissions')->whereIn('id', $roleIds)->get();
        if ($roles->contains(fn (Role $role) => $role->name === 'SA')) {
            abort(403, 'لا يمكنك إسناد دور Super Admin.');
        }

        $hasElevatedPermission = $roles->contains(function (Role $role): bool {
            return $role->permissions->contains(function ($permission): bool {
                return $permission->pivot->scope === 'organization'
                    || $permission->key === 'settings.manage_permissions';
            });
        });

        if ($hasElevatedPermission) {
            abort(403, 'لا يمكنك إسناد صلاحيات على مستوى المؤسسة أو إدارة الصلاحيات.');
        }
    }

    private function isLastActiveSuperAdmin(User $targetUser): bool
    {
        if (! $targetUser->is_active || ! $targetUser->hasRole('SA')) {
            return false;
        }

        return User::query()->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->where('name', 'SA'))
            ->count() <= 1;
    }

    private function lockSuperAdminRole(): ?Role
    {
        return Role::query()->where('name', 'SA')->lockForUpdate()->first();
    }
}
