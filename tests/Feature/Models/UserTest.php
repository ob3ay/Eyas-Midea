<?php

namespace Tests\Feature\Models;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_active_permission_grants_its_configured_scope(): void
    {
        [$user] = $this->userWithPermission();

        $this->assertSame('department', $user->highestScopeFor('projects.view'));
        $this->assertTrue($user->hasPermission('projects.view'));
    }

    public function test_soft_deleted_permission_does_not_grant_permission(): void
    {
        [$user, $permission] = $this->userWithPermission();

        $permission->delete();

        $this->assertSame(null, $user->highestScopeFor('projects.view'));
        $this->assertFalse($user->hasPermission('projects.view'));
    }

    public function test_soft_deleted_role_does_not_grant_permission(): void
    {
        [$user,, $role] = $this->userWithPermission();

        $role->delete();

        $this->assertSame(null, $user->highestScopeFor('projects.view'));
        $this->assertFalse($user->hasPermission('projects.view'));
    }

    public function test_section_scope_only_includes_employees_in_the_same_department_and_section(): void
    {
        $department = Department::create(['name' => 'Section Scope Department']);
        $otherDepartment = Department::create(['name' => 'Other Scope Department']);
        $section = \App\Models\Section::create(['name' => 'Scoped Section', 'department_id' => $department->id]);
        $otherSection = \App\Models\Section::create(['name' => 'Other Section', 'department_id' => $department->id]);

        $ownEmployee = Employee::create([
            'full_name' => 'Section Manager', 'department_id' => $department->id, 'section_id' => $section->id,
        ]);
        $sameSection = Employee::create([
            'full_name' => 'Same Section Employee', 'department_id' => $department->id, 'section_id' => $section->id,
        ]);
        $otherSectionEmployee = Employee::create([
            'full_name' => 'Other Section Employee', 'department_id' => $department->id, 'section_id' => $otherSection->id,
        ]);
        $otherDepartmentEmployee = Employee::create([
            'full_name' => 'Other Department Employee', 'department_id' => $otherDepartment->id,
        ]);
        $user = User::create([
            'employee_id' => $ownEmployee->id,
            'username' => 'section-manager',
            'email' => 'section-manager@example.test',
            'password' => 'password',
        ]);
        $role = Role::create(['name' => 'SECTION_SCOPE_TEST']);
        $permission = Permission::create(['key' => 'employees.view']);
        $user->roles()->attach($role->id);
        $role->permissions()->attach($permission->id, ['scope' => 'section']);

        $this->assertSame('section', $user->highestScopeFor('employees.view'));
        $this->assertTrue($user->allowsEmployeeWithinScope('employees.view', $sameSection));
        $this->assertFalse($user->allowsEmployeeWithinScope('employees.view', $otherSectionEmployee));
        $this->assertFalse($user->allowsEmployeeWithinScope('employees.view', $otherDepartmentEmployee));
        $this->assertSame(
            [$ownEmployee->id, $sameSection->id],
            $user->employeesWithinScope('employees.view')->orderBy('id')->pluck('id')->all(),
        );
    }

    public function test_employee_visibility_matrix_respects_each_permission_scope(): void
    {
        $department = Department::create(['name' => 'Scope Matrix Department']);
        $otherDepartment = Department::create(['name' => 'Other Scope Matrix Department']);
        $section = \App\Models\Section::create(['name' => 'Scope Matrix Section', 'department_id' => $department->id]);
        $otherSection = \App\Models\Section::create(['name' => 'Other Scope Matrix Section', 'department_id' => $department->id]);
        $targets = [
            Employee::create(['full_name' => 'Matrix Owner', 'department_id' => $department->id, 'section_id' => $section->id]),
            Employee::create(['full_name' => 'Same Matrix Section', 'department_id' => $department->id, 'section_id' => $section->id]),
            Employee::create(['full_name' => 'Other Matrix Section', 'department_id' => $department->id, 'section_id' => $otherSection->id]),
            Employee::create(['full_name' => 'Other Matrix Department', 'department_id' => $otherDepartment->id]),
        ];
        $permission = Permission::create(['key' => 'employees.view']);
        $expectedByScope = [
            'self' => ['owner' => 0, 'employees' => [$targets[0]->id]],
            'section' => ['owner' => 1, 'employees' => [$targets[0]->id, $targets[1]->id]],
            'department' => ['owner' => 2, 'employees' => [$targets[0]->id, $targets[1]->id, $targets[2]->id]],
            'organization' => ['owner' => 3, 'employees' => array_map(fn (Employee $employee): int => $employee->id, $targets)],
        ];

        foreach ($expectedByScope as $scope => $expectation) {
            $user = User::create([
                'employee_id' => $targets[$expectation['owner']]->id,
                'username' => "matrix-{$scope}",
                'email' => "matrix-{$scope}@example.test",
                'password' => 'password',
            ]);
            $role = Role::create(['name' => 'MATRIX_'.strtoupper($scope)]);
            $user->roles()->attach($role->id);
            $role->permissions()->attach($permission->id, ['scope' => $scope]);

            $this->assertSame(
                $expectation['employees'],
                $user->employeesWithinScope('employees.view')->orderBy('id')->pluck('id')->all(),
                "Unexpected employee boundary for {$scope} scope.",
            );
        }
    }

    /**
     * @return array{User, Permission, Role}
     */
    private function userWithPermission(): array
    {
        $department = Department::create(['name' => 'Test Department']);
        $employee = Employee::create([
            'full_name' => 'Test Employee',
            'department_id' => $department->id,
        ]);
        $user = User::create([
            'employee_id' => $employee->id,
            'username' => 'test-user',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $role = Role::create(['name' => 'TEST']);
        $permission = Permission::create(['key' => 'projects.view']);

        $user->roles()->attach($role->id);
        $role->permissions()->attach($permission->id, ['scope' => 'department']);

        return [$user, $permission, $role];
    }
}
