<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Section;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'تطوير البرمجيات' => [
                'code' => 'DEV',
                'sections' => ['تطوير الويب', 'تطوير الهواتف', 'جودة البرمجيات']
            ],
            'الإنتاج الإعلامي' => [
                'code' => 'MED',
                'sections' => ['المونتاج', 'التصميم الجرافيكي', 'كتابة المحتوى']
            ],
            'الموارد البشرية' => [
                'code' => 'HR',
                'sections' => ['الاستقطاب', 'إدارة الأداء']
            ],
        ];

        foreach ($departments as $deptName => $data) {
            $department = Department::create([
                'name' => $deptName,
                'code' => $data['code'],
                'description' => "قسم " . $deptName,
            ]);

            foreach ($data['sections'] as $sectionName) {
                Section::create([
                    'department_id' => $department->id,
                    'name' => $sectionName,
                ]);
            }
        }
    }
}   
