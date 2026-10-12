<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RolePermissionController extends Controller
{
    public function update(Request $request, Role $role, Permission $permission): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('SA') || $user->highestScopeFor('settings.manage_permissions') !== 'organization') {
            abort(403, 'ما عندك صلاحية إدارة الأدوار والصلاحيات.');
        }

        if ($role->name === 'SA' && ! $user->hasRole('SA')) {
            abort(403, 'لا يمكنك تعديل صلاحيات دور Super Admin.');
        }

        $data = $request->validate([
            'scope' => ['present', 'nullable', Rule::in(['self', 'section', 'department', 'organization'])],
        ]);

        if (($data['scope'] ?? null) === 'organization' && ! $user->hasRole('SA')) {
            abort(403, 'منح نطاق المؤسسة الكاملة متاح للسوبر أدمن فقط.');
        }

        if (($data['scope'] ?? null) === 'section'
            && ! preg_match('/^(employees|tasks|attendance|performance|reports|users|requests)\\./', $permission->key)) {
            abort(422, 'نطاق القسم غير مدعوم لهذه الصلاحية.');
        }

        if ($permission->key === 'settings.manage_permissions' && ! $user->hasRole('SA')) {
            abort(403, 'لا يمكنك منح صلاحية إدارة الصلاحيات.');
        }

        DB::transaction(function () use ($role, $permission, $data): void {
            $lockedRole = Role::query()->lockForUpdate()->findOrFail($role->id);
            $oldScope = $lockedRole->permissions()
                ->where('permissions.id', $permission->id)
                ->first()?->pivot?->scope;

            if (empty($data['scope'] ?? null)) {
                $lockedRole->permissions()->detach($permission->id);
            } else {
                $lockedRole->permissions()->syncWithoutDetaching([
                    $permission->id => ['scope' => $data['scope']],
                ]);
            }

            AuditLog::record('UpdateRolePermission', 'Role', $lockedRole->id, [
                'permission' => $permission->key,
                'old_scope' => $oldScope,
            ], [
                'permission' => $permission->key,
                'new_scope' => $data['scope'] ?? null,
            ]);
        });

        $user->forgetAuthorizationCache();

        return back()->with('success', "تم تحديث صلاحية «{$permission->display_name}» لدور «{$role->display_name}»");
    }
}
