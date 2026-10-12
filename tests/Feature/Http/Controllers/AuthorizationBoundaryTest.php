<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRequest;
use App\Models\Permission;
use App\Models\PerformanceEvaluation;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthorizationBoundaryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_employee_profile_does_not_expose_data_from_ungranted_modules(): void
    {
        $department = Department::create(['name' => 'Private Profile Department']);
        $employee = $this->createEmployee('Profile Owner', $department);
        $user = $this->createUserWithPermissions('profile-owner', $employee, [
            'employees.view' => 'self',
        ]);

        Task::create([
            'title' => 'Secret Task Title',
            'assignee_employee_id' => $employee->id,
            'created_by' => $employee->id,
            'status' => 'To-Do',
        ]);
        Project::create([
            'title' => 'Secret Project Title',
            'department_id' => $department->id,
            'owner_employee_id' => $employee->id,
            'start_date' => today()->toDateString(),
            'status' => 'in_progress',
            'completion_percentage' => 10,
        ]);
        AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => today()->toDateString(),
            'status' => 'Present',
        ]);
        PerformanceEvaluation::create([
            'employee_id' => $employee->id,
            'evaluator_user_id' => $user->id,
            'evaluation_date' => today()->toDateString(),
            'period' => 'test period',
            'score' => 95,
            'notes' => 'Secret performance note',
            'is_finalized' => true,
        ]);

        $this->actingAs($user)
            ->get(route('employees.show', $employee))
            ->assertOk()
            ->assertDontSee('Secret Task Title')
            ->assertDontSee('Secret Project Title')
            ->assertDontSee('Secret performance note');
    }

    public function test_employee_without_request_approval_permission_cannot_approve_a_request(): void
    {
        $department = Department::create(['name' => 'Request Department']);
        $managerEmployee = $this->createEmployee('Manager', $department);
        $requestOwner = $this->createEmployee('Request Owner', $department);
        $user = $this->createUserWithPermissions('request-manager', $managerEmployee, [
            'employees.view' => 'department',
        ]);
        $employeeRequest = EmployeeRequest::create([
            'employee_id' => $requestOwner->id,
            'type' => 'other',
            'title' => 'Pending request',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('requests.approve', $employeeRequest))
            ->assertForbidden();

        $this->assertSame('pending', $employeeRequest->fresh()->status);
    }

    public function test_request_approval_permission_is_limited_to_its_scope(): void
    {
        $department = Department::create(['name' => 'Manager Department']);
        $otherDepartment = Department::create(['name' => 'Other Manager Department']);
        $managerEmployee = $this->createEmployee('Manager With Permission', $department);
        $localEmployee = $this->createEmployee('Local Requester', $department);
        $outsideEmployee = $this->createEmployee('Outside Requester', $otherDepartment);
        $user = $this->createUserWithPermissions('scoped-approver', $managerEmployee, [
            'requests.approve' => 'department',
        ]);
        $localRequest = EmployeeRequest::create([
            'employee_id' => $localEmployee->id,
            'type' => 'other',
            'title' => 'Local pending request',
            'status' => 'pending',
        ]);
        $outsideRequest = EmployeeRequest::create([
            'employee_id' => $outsideEmployee->id,
            'type' => 'other',
            'title' => 'Outside pending request',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('requests.approve', $outsideRequest))
            ->assertForbidden();
        $this->assertSame('pending', $outsideRequest->fresh()->status);

        $this->post(route('requests.approve', $localRequest))
            ->assertRedirect(route('requests.index'));
        $this->assertSame('approved', $localRequest->fresh()->status);
    }

    public function test_a_request_can_only_be_decided_once_and_the_decision_is_audited(): void
    {
        $department = Department::create(['name' => 'Decision Department']);
        $managerEmployee = $this->createEmployee('Decision Manager', $department);
        $requestOwner = $this->createEmployee('Decision Requester', $department);
        $user = $this->createUserWithPermissions('decision-manager', $managerEmployee, [
            'requests.approve' => 'department',
            'requests.reject' => 'department',
        ]);
        $employeeRequest = EmployeeRequest::create([
            'employee_id' => $requestOwner->id,
            'type' => 'other',
            'title' => 'One decision only',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('requests.approve', $employeeRequest))
            ->assertRedirect(route('requests.index'));

        $this->post(route('requests.reject', $employeeRequest))
            ->assertStatus(409);

        $this->assertSame('approved', $employeeRequest->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action_type' => 'Approved',
            'entity_type' => 'EmployeeRequest',
            'entity_id' => $employeeRequest->id,
        ]);
    }

    public function test_request_list_is_paginated_while_statistics_cover_the_full_scope(): void
    {
        $department = Department::create(['name' => 'Paginated Request Department']);
        $employee = $this->createEmployee('Paginated Request Owner', $department);
        $user = $this->createUserWithPermissions('paginated-request-owner', $employee, [
            'requests.view' => 'self',
        ]);

        for ($index = 1; $index <= 26; $index++) {
            EmployeeRequest::create([
                'employee_id' => $employee->id,
                'type' => 'other',
                'title' => "Request {$index}",
                'status' => $index === 1 ? 'approved' : 'pending',
            ]);
        }

        $this->actingAs($user)
            ->get(route('requests.index'))
            ->assertOk()
            ->assertViewHas('requests', fn ($requests): bool => $requests->count() === 25 && $requests->total() === 26)
            ->assertViewHas('stats', fn (array $stats): bool => $stats['total'] === 26 && $stats['pending'] === 25 && $stats['approved'] === 1);
    }

    public function test_section_scoped_reports_only_aggregate_records_within_the_section(): void
    {
        $department = Department::create(['name' => 'Report Department']);
        $otherDepartment = Department::create(['name' => 'Other Report Department']);
        $section = \App\Models\Section::create(['name' => 'Report Section', 'department_id' => $department->id]);
        $otherSection = \App\Models\Section::create(['name' => 'Other Report Section', 'department_id' => $department->id]);
        $manager = $this->createEmployee('Report Manager', $department, $section);
        $colleague = $this->createEmployee('Report Colleague', $department, $section);
        $outsideSection = $this->createEmployee('Outside Report Section', $department, $otherSection);
        $outsideDepartment = $this->createEmployee('Outside Report Department', $otherDepartment);
        $user = $this->createUserWithPermissions('section-report-viewer', $manager, [
            'reports.view' => 'section',
        ]);

        $this->createProject('Scoped report project', $department, $colleague);
        $this->createProject('Outside report project', $otherDepartment, $outsideDepartment);
        $this->createTask('Scoped report task', $colleague, 'To-Do');
        $this->createTask('Other section report task', $outsideSection, 'Under Review');
        $this->createTask('Outside report task', $outsideDepartment, 'Under Review');

        $this->actingAs($user)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertViewHas('employeesByStatus', fn ($counts): bool => $counts->sum() === 2)
            ->assertViewHas('tasksByStatus', fn ($counts): bool => $counts->sum() === 1 && $counts->get('To-Do') === 1)
            ->assertViewHas('projectsByStatus', fn ($counts): bool => $counts->sum() === 1);
    }

    public function test_self_scoped_department_creation_cannot_create_an_organization_resource(): void
    {
        $department = Department::create(['name' => 'Self Scope Department']);
        $employee = $this->createEmployee('Self Scoped User', $department);
        $user = $this->createUserWithPermissions('self-department-creator', $employee, [
            'departments.create' => 'self',
        ]);

        $this->actingAs($user)
            ->post(route('departments.store'), [
                'name' => 'Unauthorized Department',
                'code' => 'NOPE',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('departments', ['name' => 'Unauthorized Department']);
    }

    private function createEmployee(string $name, Department $department, ?\App\Models\Section $section = null): Employee
    {
        return Employee::create([
            'full_name' => $name,
            'department_id' => $department->id,
            'section_id' => $section?->id,
            'employment_status' => 'active',
        ]);
    }

    /** @param array<string, string> $permissions */
    private function createUserWithPermissions(string $username, Employee $employee, array $permissions): User
    {
        $user = User::create([
            'employee_id' => $employee->id,
            'username' => $username,
            'email' => "{$username}@example.test",
            'password' => 'password',
            'is_active' => true,
        ]);
        $role = Role::create(['name' => 'ROLE_'.$username]);
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
            'end_date' => today()->addDay()->toDateString(),
            'status' => 'in_progress',
            'completion_percentage' => 10,
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
}
