<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\Employee;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $employees = Employee::all();

        if ($employees->isEmpty()) return;

        for ($p = 1; $p <= 5; $p++) {
            $manager = $employees->random();

            $project = Project::create([
                'title'             => 'مشروع ' . $faker->catchPhrase,
                'description'       => $faker->paragraph,
                'department_id'     => $manager->department_id,
                'owner_employee_id' => $manager->id,
                'status'            => 'Active',
                'start_date'        => now()->subDays(10),
                'end_date'          => now()->addDays(30),
            ]);

            // إضافة مهام للمشروع
            for ($t = 1; $t <= 4; $t++) {
                Task::create([
                    'project_id'           => $project->id,
                    'title'                => 'مهمة: ' . $faker->realText(30),
                    'description'          => $faker->text(100),
                    'assignee_employee_id' => $employees->random()->id, // تم التعديل
                    'created_by'           => $manager->id,
                    'priority'             => $faker->randomElement(['Low', 'Medium', 'High', 'Urgent']), // تم التعديل
                    'status'               => $faker->randomElement(['To-Do', 'In Progress', 'Under Review', 'Completed']), // تم التعديل
                    'due_datetime'         => now()->addDays(rand(2, 15)), // تم التعديل
                ]);
            }
        }
    }
}
