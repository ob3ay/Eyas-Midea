<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerformanceEvaluation;
use App\Models\Employee;
use App\Models\User;
use Faker\Factory as Faker;

class PerformanceEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $employees = Employee::all();
        $users = User::all();

        if ($employees->isEmpty() || $users->isEmpty()) return;

        foreach ($employees as $employee) {
            $evaluator = $users->random();

            PerformanceEvaluation::create([
                'employee_id'       => $employee->id,
                'evaluator_user_id' => $evaluator->id,       // تم التعديل
                'evaluation_date'   => now()->subDays(5),    // حقل إلزامي تم إضافته
                'period'            => 'Q3-2026',
                'score'             => $faker->randomFloat(2, 60, 100),
                'notes'             => $faker->paragraph,     // تم استبدال feedback بـ notes
                'is_finalized'      => true,
            ]);
        }
    }
}
