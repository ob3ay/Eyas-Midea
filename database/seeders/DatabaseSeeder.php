<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,          // 1. الأقسام والشعب
            EmployeeSeeder::class,            // 2. الموظفون والمستخدمون
            ProjectSeeder::class,             // 3. المشاريع والمهام
            AttendanceSeeder::class,          // 4. الحضور وتعديل البصمة
            PerformanceEvaluationSeeder::class, // 5. تقييمات الأداء
        ]);
    }
}
