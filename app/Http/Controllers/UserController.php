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
    public function index()
    {
        $users = User::with([
            'employee.department',
            'employee.section',
            'roles.permissions',
        ])
            ->orderByDesc('is_active')
            ->orderBy('username')
            ->get();

        $roles = Role::with('permissions')
            ->orderBy('display_name')
            ->get();

        $permissions = Permission::orderBy('display_name')
            ->get();

        $employees = Employee::whereDoesntHave('user')
            ->orderBy('full_name')
            ->get();

        // البحث عن مستخدم Super Admin
        $superAdmin = $users->first(function ($user) {
            return $user->roles->contains(function ($role) {
                return strtolower($role->name) === 'super_admin'
                    || strtolower((string) $role->display_name) === 'super admin';
            });
        });

        // إذا لم يوجد Super Admin بالاسم، نبحث عن مستخدم
        // لديه صلاحية على مستوى المؤسسة
        if (!$superAdmin) {
            $superAdmin = $users->first(function ($user) {
                return $user->roles->contains(function ($role) {
                    return $role->permissions->contains(function ($permission) {
                        return $permission->pivot->scope === 'organization';
                    });
                });
            });
        }

        return view('users.index', compact(
            'users',
            'roles',
            'permissions',
            'employees',
            'superAdmin'
        ));
    }


    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                'unique:users,employee_id',
            ],

            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'roles' => [
                'nullable',
                'array',
            ],

            'roles.*' => [
                'exists:roles,id',
            ],
        ]);

        $user = User::create([
            'employee_id' => $data['employee_id'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->roles()->sync($data['roles'] ?? []);

        return back()->with(
            'success',
            'تم إنشاء حساب المستخدم بنجاح'
        );
    }


    public function update(
        Request $request,
        User $user
    ): RedirectResponse {

        $data = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('users', 'employee_id')
                    ->ignore($user->id),
            ],

            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')
                    ->ignore($user->id),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'roles' => [
                'nullable',
                'array',
            ],

            'roles.*' => [
                'exists:roles,id',
            ],
        ]);

        $user->employee_id = $data['employee_id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->is_active = $request->boolean('is_active');

        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        $user->roles()->sync($data['roles'] ?? []);

        return back()->with(
            'success',
            'تم تحديث حساب المستخدم بنجاح'
        );
    }


    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with(
            'success',
            'تم حذف حساب المستخدم'
        );
    }
}
