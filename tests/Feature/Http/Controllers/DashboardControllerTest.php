<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRequest;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Section;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_department_dashboard_only_shows_data_from_the_users_scope(): void
    {
        $department = Department::create(['name' => 'Local Department']);
        $otherDepartment = Department::create(['name' => 'Other Department']);
        $managerEmployee = $this->createEmployee('Local Manager', $department);
        $colleague = $this->createEmployee('Local Colleague', $department);
        $otherEmployee = $this->createEmployee('Other Employee', $otherDepartment);
        $manager = $this->createUserWithPermissions(
            'local-manager',
            $managerEmployee,
            'DP',
            [
                'employees.view' => 'department',
                'departments.view' => 'department',
                'projects.view' => 'department',
                'tasks.view' => 'department',
                'attendance.view' => 'department',
                'requests.approve' => 'department',
            ],
        );

        $localProject = $this->createProject('Local project', $department, $colleague);
        $this->createProject('Other project', $otherDepartment, $otherEmployee);
        $this->createTask('Local task', $colleague, 'Under Review');
        $this->createTask('Other task', $otherEmployee, 'Under Review');
        $this->createAttendance($managerEmployee, 'Present');
        $this->createAttendance($otherEmployee, 'Present');

        EmployeeRequest::create([
            'employee_id' => $colleague->id,
            'type' => 'other',
            'title' => 'Local request',
            'status' => 'pending',
        ]);
        EmployeeRequest::create([
            'employee_id' => $otherEmployee->id,
            'type' => 'other',
            'title' => 'Other request',
            'status' => 'pending',
        ]);

        AuditLog::create([
            'user_id' => $manager->id,
            'action_type' => 'LocalAction',
            'entity_type' => 'Task',
            'entity_id' => 1,
        ]);
        AuditLog::create([
            'user_id' => null,
            'action_type' => 'OutsideScopeAction',
            'entity_type' => 'SecretRecord',
            'entity_id' => 2,
        ]);

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('employeeCount', 2)
            ->assertViewHas('departmentCount', 1)
            ->assertViewHas('projectCount', 1)
            ->assertViewHas('taskCount', 1)
            ->assertViewHas('underReviewCount', 1)
            ->assertViewHas('attendanceRate', 50.0)
            ->assertViewHas('presentTodayCount', 1)
            ->assertViewHas('pendingRequestsCount', 1)
            ->assertViewHas('activeProjects', fn ($projects): bool => $projects->sole()->is($localProject))
            ->assertViewHas('recentActivities', fn ($activities): bool => $activities->sole()->user_id === $manager->id)
            ->assertDontSee('Other project')
            ->assertDontSee('SecretRecord');
    }

    public function test_employee_dashboard_does_not_fall_back_to_other_users_activity(): void
    {
        $department = Department::create(['name' => 'Employee Department']);
        $otherDepartment = Department::create(['name' => 'Other Employee Department']);
        $employee = $this->createEmployee('Employee', $department);
        $otherEmployee = $this->createEmployee('Other Employee', $otherDepartment);
        $user = $this->createUserWithPermissions(
            'employee-user',
            $employee,
            'EMP',
            [
                'employees.view' => 'self',
                'projects.view' => 'organization',
                'tasks.view' => 'organization',
                'attendance.view' => 'self',
                'performance.view' => 'self',
            ],
        );
        $ownProject = $this->createProject('My project', $department, $employee);
        $this->createProject('Other employee project', $otherDepartment, $otherEmployee);
        $this->createTask('My task', $employee, 'In Progress');
        $this->createTask('Other employee task', $otherEmployee, 'Under Review');

        AuditLog::create([
            'user_id' => null,
            'action_type' => 'OutsideScopeAction',
            'entity_type' => 'SecretRecord',
            'entity_id' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('isEmployee', true)
            ->assertViewHas('projectCount', 1)
            ->assertViewHas('taskCount', 1)
            ->assertViewHas('underReviewCount', 0)
            ->assertViewHas('activeProjects', fn ($projects): bool => $projects->sole()->is($ownProject))
            ->assertViewHas('recentActivities', fn ($activities): bool => $activities->isEmpty())
            ->assertDontSee('Other employee project')
            ->assertDontSee('SecretRecord');
    }

    public function test_organization_dashboard_keeps_organization_wide_metrics_for_organization_scope(): void
    {
        $department = Department::create(['name' => 'First Department']);
        $otherDepartment = Department::create(['name' => 'Second Department']);
        $employee = $this->createEmployee('First Employee', $department);
        $otherEmployee = $this->createEmployee('Second Employee', $otherDepartment);
        $user = $this->createUserWithPermissions(
            'organization-viewer',
            $employee,
            'EX',
            [
                'employees.view' => 'organization',
                'departments.view' => 'organization',
                'projects.view' => 'organization',
                'tasks.view' => 'organization',
                'attendance.view' => 'organization',
            ],
        );

        $this->createProject('Organization project', $otherDepartment, $otherEmployee);
        $this->createTask('Organization task', $otherEmployee, 'To-Do');
        $this->createAttendance($otherEmployee, 'Present');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('employeeCount', 2)
            ->assertViewHas('departmentCount', 2)
            ->assertViewHas('projectCount', 1)
            ->assertViewHas('taskCount', 1)
            ->assertViewHas('attendanceRate', 50.0);
    }

    public function test_section_manager_dashboard_limits_employees_tasks_attendance_and_requests_to_their_section(): void
    {
        $department = Department::create(['name' => 'Section Department']);
        $otherDepartment = Department::create(['name' => 'Other Section Department']);
        $section = Section::create([
            'name' => 'Managed Section',
            'department_id' => $department->id,
        ]);
        $otherSection = Section::create([
            'name' => 'Other Section',
            'department_id' => $department->id,
        ]);
        $managerEmployee = $this->createEmployee('Section Manager', $department, $section);
        $colleague = $this->createEmployee('Section Colleague', $department, $section);
        $otherSectionEmployee = $this->createEmployee('Other Section Employee', $department, $otherSection);
        $otherDepartmentEmployee = $this->createEmployee('Other Department Employee', $otherDepartment);
        $manager = $this->createUserWithPermissions(
            'section-manager',
            $managerEmployee,
            'SH',
            [
                'employees.view' => 'section',
                'tasks.view' => 'section',
                'attendance.view' => 'section',
                'requests.approve' => 'section',
            ],
        );

        $this->createTask('Managed section task', $colleague, 'To-Do');
        $this->createTask('Other section task', $otherSectionEmployee, 'Under Review');
        $this->createTask('Other department task', $otherDepartmentEmployee, 'Under Review');
        $this->createAttendance($managerEmployee, 'Present');
        $this->createAttendance($otherSectionEmployee, 'Present');
        EmployeeRequest::create([
            'employee_id' => $colleague->id,
            'type' => 'other',
            'title' => 'Managed section request',
            'status' => 'pending',
        ]);
        EmployeeRequest::create([
            'employee_id' => $otherSectionEmployee->id,
            'type' => 'other',
            'title' => 'Other section request',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('isEmployee', false)
            ->assertViewHas('employeeCount', 2)
            ->assertViewHas('taskCount', 1)
            ->assertViewHas('underReviewCount', 0)
            ->assertViewHas('attendanceRate', 50.0)
            ->assertViewHas('pendingRequestsCount', 1);
    }

    public function test_dashboard_hides_metrics_for_modules_without_view_permissions(): void
    {
        $department = Department::create(['name' => 'Restricted Department']);
        $employee = $this->createEmployee('Restricted Employee', $department);
        $user = $this->createUserWithPermissions(
            'restricted-user',
            $employee,
            'LIMITED',
            ['projects.create' => 'self'],
        );

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('employeeCount', 0)
            ->assertViewHas('projectCount', 0)
            ->assertViewHas('taskCount', 0)
            ->assertViewHas('canViewEmployees', false)
            ->assertViewHas('canViewProjects', false)
            ->assertViewHas('canViewTasks', false)
            ->assertViewHas('canCreateProject', false)
            ->assertDontSee('المشاريع البرمجية والإعلامية')
            ->assertDontSee('المهام في دورة العمل');
    }

    private function createEmployee(string $name, Department $department, ?Section $section = null): Employee
    {
        return Employee::create([
            'full_name' => $name,
            'department_id' => $department->id,
            'section_id' => $section?->id,
            'employment_status' => 'active',
        ]);
    }

    /**
     * @param  array<string, string>  $permissions
     */
    private function createUserWithPermissions(
        string $username,
        Employee $employee,
        string $roleName,
        array $permissions,
    ): User {
        $user = User::create([
            'employee_id' => $employee->id,
            'username' => $username,
            'email' => "{$username}@example.test",
            'password' => 'password',
            'is_active' => true,
        ]);
        $role = Role::create(['name' => $roleName]);
        $user->roles()->attach($role->id);

        foreach ($permissions as $key => $scope) {
            $permission = Permission::firstOrCreate(['key' => $key]);
            $role->permissions()->attach($permission->id, ['scope' => $scope]);
        }

        return $user;
    }

    private function createProject(string $title, Department $department, Employee $owner): Project
    {
        return Project::create([
            'title' => $title,
            'department_id' => $department->id,
            'owner_employee_id' => $owner->id,
            'start_date' => today()->toDateString(),
            'status' => 'in_progress',
            'completion_percentage' => 25,
        ]);
    }

    private function createTask(string $title, Employee $assignee, string $status): Task
    {
        return Task::create([
            'title' => $title,
            'assignee_employee_id' => $assignee->id,
            'created_by' => $assignee->id,
            'status' => $status,
        ]);
    }

    private function createAttendance(Employee $employee, string $status): AttendanceRecord
    {
        return AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => today()->toDateString(),
            'status' => $status,
        ]);
    }
}
