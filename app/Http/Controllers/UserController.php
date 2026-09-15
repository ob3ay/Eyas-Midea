<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $employees = Employee::whereDoesntHave('user')
            ->when($scope !== 'organization', function ($query) use ($user) {
                $query->where('department_id', $user->employee?->department_id ?? 0);
            })
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
            'employee_id' => ['required', 'exists:employees,id', 'unique:users,employee_id'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertEmployeeWithinScope($user, $employee, $scope);
        $this->assertAssignableRoles($user, $data['roles'] ?? []);

        $newUser = User::create([
            'employee_id' => $data['employee_id'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($user->hasPermission('settings.manage_permissions')) {
            $newUser->roles()->sync($data['roles'] ?? []);
        }

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
            'employee_id' => ['required', 'exists:employees,id', Rule::unique('users', 'employee_id')->ignore($targetUser->id)],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($targetUser->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertEmployeeWithinScope($user, $employee, $scope);
        $this->assertAssignableRoles($user, $data['roles'] ?? []);

        $targetUser->employee_id = $data['employee_id'];
        $targetUser->username = $data['username'];
        $targetUser->email = $data['email'];
        $targetUser->is_active = $request->boolean('is_active');

        if (! empty($data['password'])) {
            $targetUser->password = $data['password'];
        }

        $targetUser->save();

        if ($user->hasPermission('settings.manage_permissions')) {
            $targetUser->roles()->sync($data['roles'] ?? []);
        }

        return back()->with('success', 'تم تحديث حساب المستخدم بنجاح');
    }

    public function destroy(Request $request, User $targetUser): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('users.delete', $targetUser, $user);

        if ($targetUser->id === $user->id) {
            return back()->with('error', 'ما فيك تحذف حسابك الخاص وأنت مسجل دخول فيه.');
        }

        if ($targetUser->hasRole('SA') && ! $user->hasRole('SA')) {
            abort(403, 'لا يمكنك حذف حساب Super Admin.');
        }

        $targetUser->delete();

        return back()->with('success', 'تم حذف حساب المستخدم');
    }

    private function authorizeScope(string $permissionKey, ?User $target = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ما عندك صلاحية لهاي العملية.');
        }

        if ($target !== null && $scope !== 'organization') {
            $ownEmployee = $user->employee;
            $targetEmployee = $target->employee;

            if (! $ownEmployee || ! $targetEmployee) {
                abort(403, 'ما فيك تدير هاد الحساب.');
            }

            if ($scope === 'self' && $target->id !== $user->id) {
                abort(403, 'فيك تدير حسابك الشخصي بس.');
            }

            if ($scope === 'department') {
                if ($user->hasRole('SH')) {
                    if ($targetEmployee->section_id !== $ownEmployee->section_id) {
                        abort(403, 'فيك تدير حسابات موظفين قسمك بس.');
                    }
                } elseif ($targetEmployee->department_id !== $ownEmployee->department_id) {
                    abort(403, 'فيك تدير حسابات موظفين دائرتك بس.');
                }
            }
        }

        return $scope;
    }

    private function scopedUsersQuery(User $user, string $scope)
    {
        $query = User::query();

        if ($scope === 'organization') {
            return $query;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            return $query->whereRaw('1 = 0');
        }

        if ($scope === 'self') {
            return $query->where('id', $user->id);
        }

        if ($user->hasRole('SH')) {
            return $query->whereHas('employee', fn ($q) => $q->where('section_id', $ownEmployee->section_id));
        }

        return $query->whereHas('employee', fn ($q) => $q->where('department_id', $ownEmployee->department_id));
    }

    private function assertEmployeeWithinScope(User $user, Employee $employee, string $scope): void
    {
        if ($scope === 'organization') {
            return;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee || $employee->department_id !== $ownEmployee->department_id) {
            abort(403, 'لا يمكنك إدارة حساب موظف خارج نطاق دائرتك.');
        }

        if ($scope === 'self' && $employee->id !== $ownEmployee->id) {
            abort(403, 'يمكنك إدارة حسابك الشخصي فقط.');
        }
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
}
