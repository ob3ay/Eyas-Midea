<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $departments = Department::all();

        $count = 1;

        foreach ($departments as $dept) {
            $sections = Section::where('department_id', $dept->id)->get();

            for ($i = 1; $i <= 3; $i++) {
                $section = $sections->isNotEmpty() ? $sections->random() : null;
                $fullName = $faker->firstName . ' ' . $faker->lastName;
                $username = strtolower($faker->unique()->userName);

                $employee = Employee::create([
                    'employee_code'     => 'EMP-' . str_pad($count++, 3, '0', STR_PAD_LEFT),
                    'full_name'         => $fullName,
                    'national_id'       => $faker->unique()->numerify('1#########'),
                    'department_id'     => $dept->id,
                    'section_id'        => $section ? $section->id : null,
                    'position'          => $faker->jobTitle,
                    'phone'             => $faker->phoneNumber,
                    'employment_status' => 'active',
                    'hire_date'         => $faker->date(),
                ]);

                User::create([
                    'username'    => $username,
                    'email'       => $username . '@eyasmedia.com',
                    'password'    => Hash::make('password123'),
                    'employee_id' => $employee->id,
                    'is_active'   => true,
                ]);
            }
        }
    }
}
