<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_temporary_password_requires_change_before_accessing_the_portal(): void
    {
        $department = Department::create(['name' => 'Password Flow Department']);
        $employee = Employee::create([
            'full_name' => 'Password Flow Employee',
            'department_id' => $department->id,
        ]);
        $user = User::create([
            'employee_id' => $employee->id,
            'username' => 'temporary-password-user',
            'email' => 'temporary-password@example.test',
            'password' => 'Temporary123!Pass',
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $this->post(route('login.post'), [
            'email' => 'temporary-password@example.test',
            'password' => 'Temporary123!Pass',
        ])->assertRedirect(route('password.change'));

        $this->get(route('dashboard'))->assertRedirect(route('password.change'));
        $this->get(route('password.change'))->assertOk();

        $this->put(route('password.change.update'), [
            'current_password' => 'Temporary123!Pass',
            'password' => 'NewSecure123!Pass',
            'password_confirmation' => 'NewSecure123!Pass',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'must_change_password' => false]);
        $this->assertTrue(Hash::check('NewSecure123!Pass', $user->fresh()->password));
        $this->get(route('dashboard'))->assertOk();
    }
}
