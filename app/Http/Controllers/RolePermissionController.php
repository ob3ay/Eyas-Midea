<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolePermissionController extends Controller
{
    public function update(Request $request, Role $role, Permission $permission): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasPermission('settings.manage_permissions')) {
            abort(403, 'ما عندك صلاحية إدارة الأدوار والصلاحيات.');
        }

        $data = $request->validate([
            'scope' => ['nullable', Rule::in(['self', 'department', 'organization'])],
        ]);

        $oldScope = $role->permissions()->where('permissions.id', $permission->id)->first()?->pivot?->scope;

        if (empty($data['scope'])) {
            $role->permissions()->detach($permission->id);
        } else {
            $role->permissions()->syncWithoutDetaching([
                $permission->id => ['scope' => $data['scope']],
            ]);
        }

        AuditLog::record('UpdateRolePermission', 'Role', $role->id, [
            'permission' => $permission->key,
            'old_scope' => $oldScope,
        ], [
            'permission' => $permission->key,
            'new_scope' => $data['scope'] ?? null,
        ]);

        return back()->with('success', "تم تحديث صلاحية «{$permission->display_name}» لدور «{$role->display_name}»");
    }
}
