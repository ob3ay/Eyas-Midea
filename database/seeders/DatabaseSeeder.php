<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) الأدوار السبعة الأساسية
        $roles = [
            ['name' => 'SA', 'display_name' => 'مدير النظام الأعلى (Super Admin)'],
            ['name' => 'EX', 'display_name' => 'المدير التنفيذي (Executive)'],
            ['name' => 'DP', 'display_name' => 'مدير دائرة (Department Manager)'],
            ['name' => 'DM', 'display_name' => 'نائب مدير (Deputy Manager)'],
            ['name' => 'SH', 'display_name' => 'رئيس قسم (Section Head)'],
            ['name' => 'EMP', 'display_name' => 'موظف (Employee)'],
            ['name' => 'TA', 'display_name' => 'مساعد إداري (Technical Assistant)'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // 2) حساب تجريبي لتسجيل الدخول (Super Admin) - بدائرة إدارية منفصلة
        $adminDept = Department::firstOrCreate(['code' => 'ADM'], ['name' => 'الإدارة العامة']);
        $adminEmployee = Employee::firstOrCreate(
            ['employee_code' => 'EMP-0000'],
            [
                'full_name' => 'مدير النظام',
                'department_id' => $adminDept->id,
                'position' => 'System Administrator',
                'employment_status' => 'active',
                'hire_date' => now(),
            ]
        );
        $adminUser = User::firstOrCreate(
            ['email' => 'saleh@eyasmedia.com'],
            [
                'employee_id' => $adminEmployee->id,
                'username' => 'admin',
                'password' => Hash::make('Passw0rd!'),
                'is_active' => true,
            ]
        );
        $superAdminRole = Role::where('name', 'SA')->first();
        if (! $adminUser->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $adminUser->roles()->attach($superAdminRole->id);
        }

        // 3) الدوائر الأربعة الأساسية (حسب التصميم المعتمد)
        $dept1 = Department::firstOrCreate(
            ['code' => 'DEPT-01'],
            ['name' => 'الإدارة العليا والتنفيذية', 'description' => 'القيادة التنفيذية، صياغة السياسات الإعلامية والتحول الرقمي، الإشراف القانوني والرقابي، والأمانة التنفيذية للمجموعة.']
        );
        $dept2 = Department::firstOrCreate(
            ['code' => 'DEPT-02'],
            ['name' => 'دائرة الإعلام', 'description' => 'الإنتاج المرئي، استوديوهات التصوير الميداني، التنسيق الرقمي والنشر الصحفي، وتغطية الأحداث وصناعة التقارير التوثيقية.']
        );
        $dept3 = Department::firstOrCreate(
            ['code' => 'DEPT-03'],
            ['name' => 'دائرة البرمجة والتقنية', 'description' => 'تطوير المنصات والأنظمة السحابية (Web)، هندسة تطبيقات الهواتف الذكية (Mobile)، وإدارة قواعد البيانات والسيرفرات وحماية المعلومات.']
        );
        $dept4 = Department::firstOrCreate(
            ['code' => 'DEPT-04'],
            ['name' => 'دائرة التسويق', 'description' => 'التسويق الرقمي وإدارة الحملات الإعلانية الممولة، العلاقات العامة والاتصال المؤسسي، وتحليل انتشار المنصات وتوسيع الجمهور المستهدف.']
        );

        // 4) الأقسام (Sections) التابعة لكل دائرة
        $secSecretariat = Section::firstOrCreate(['name' => 'الأمانة العامة والسكرتارية التنفيذية'], ['department_id' => $dept1->id]);
        $secWeb = Section::firstOrCreate(['name' => 'قسم تطوير الويب والمنصات'], ['department_id' => $dept3->id]);
        $secMobile = Section::firstOrCreate(['name' => 'قسم تطبيقات الهواتف الذكية'], ['department_id' => $dept3->id]);
        $secDbOps = Section::firstOrCreate(['name' => 'قسم هندسة السيرفرات وقواعد البيانات'], ['department_id' => $dept3->id]);
        $secVisual = Section::firstOrCreate(['name' => 'قسم الإنتاج والتصوير المرئي'], ['department_id' => $dept2->id]);
        $secDigitalCoord = Section::firstOrCreate(['name' => 'قسم التنسيق الرقمي والنشر'], ['department_id' => $dept2->id]);
        $secDigitalMkt = Section::firstOrCreate(['name' => 'قسم التسويق الرقمي'], ['department_id' => $dept4->id]);
        $secCampaigns = Section::firstOrCreate(['name' => 'قسم إدارة الحملات الإعلانية'], ['department_id' => $dept4->id]);
        $secPr = Section::firstOrCreate(['name' => 'قسم العلاقات العامة'], ['department_id' => $dept4->id]);

        // 5) الموظفون القياديون (حسب التصميم المعتمد)
        $leaders = [
            ['code' => 'EMP-0001', 'name' => 'صالح حسنين', 'position' => 'رئيس مجلس الإدارة', 'dept' => $dept1, 'section' => $secSecretariat],
            ['code' => 'EMP-0002', 'name' => 'محمد ابو شحمه', 'position' => 'المدير التنفيذي', 'dept' => $dept1, 'section' => $secSecretariat],
            ['code' => 'EMP-0003', 'name' => 'براء عمر', 'position' => 'مستشار قانوني ونائب مدير', 'dept' => $dept1, 'section' => $secSecretariat],
            ['code' => 'EMP-0004', 'name' => 'رواء صلاح', 'position' => 'سكرتارية تنفيذية', 'dept' => $dept1, 'section' => $secSecretariat],
            ['code' => 'EMP-0005', 'name' => 'البراء ابو عامر', 'position' => 'رئيس قسم الإنتاج المرئي', 'dept' => $dept2, 'section' => $secVisual],
            ['code' => 'EMP-0006', 'name' => 'لؤي بركة', 'position' => 'رئيس قسم التنسيق الرقمي', 'dept' => $dept2, 'section' => $secDigitalCoord],
            ['code' => 'EMP-0007', 'name' => 'أحمد حبيب', 'position' => 'رئيس قسم تطوير الويب', 'dept' => $dept3, 'section' => $secWeb],
            ['code' => 'EMP-0008', 'name' => 'رانيا أبو شعبان', 'position' => 'رئيسة قسم تطبيقات الهواتف', 'dept' => $dept3, 'section' => $secMobile],
            ['code' => 'EMP-0009', 'name' => 'حمزة الريس', 'position' => 'رئيس قسم قواعد البيانات', 'dept' => $dept3, 'section' => $secDbOps],
            ['code' => 'EMP-0010', 'name' => 'عمر العشي', 'position' => 'رئيس قسم التسويق الرقمي', 'dept' => $dept4, 'section' => $secDigitalMkt],
            ['code' => 'EMP-0011', 'name' => 'كمال أبو رمضان', 'position' => 'رئيس قسم الحملات الإعلانية', 'dept' => $dept4, 'section' => $secCampaigns],
            ['code' => 'EMP-0012', 'name' => 'ماجد صيام', 'position' => 'رئيس قسم العلاقات العامة', 'dept' => $dept4, 'section' => $secPr],
        ];

        $createdEmployees = [];
        foreach ($leaders as $leader) {
            $createdEmployees[$leader['code']] = Employee::firstOrCreate(
                ['employee_code' => $leader['code']],
                [
                    'full_name' => $leader['name'],
                    'department_id' => $leader['dept']->id,
                    'section_id' => $leader['section']->id,
                    'position' => $leader['position'],
                    'employment_status' => 'active',
                    'hire_date' => now(),
                ]
            );
        }

        // 6) ربط مدراء الدوائر ورؤساء الأقسام (بعد ما صار عندنا Employees فعلياً)
        $dept1->update(['manager_employee_id' => $createdEmployees['EMP-0001']->id]);
        $dept2->update(['manager_employee_id' => $createdEmployees['EMP-0005']->id]);
        $dept3->update(['manager_employee_id' => $createdEmployees['EMP-0007']->id]);
        $dept4->update(['manager_employee_id' => $createdEmployees['EMP-0010']->id]);

        $secSecretariat->update(['section_head_employee_id' => $createdEmployees['EMP-0004']->id]);
        $secVisual->update(['section_head_employee_id' => $createdEmployees['EMP-0005']->id]);
        $secDigitalCoord->update(['section_head_employee_id' => $createdEmployees['EMP-0006']->id]);
        $secWeb->update(['section_head_employee_id' => $createdEmployees['EMP-0007']->id]);
        $secMobile->update(['section_head_employee_id' => $createdEmployees['EMP-0008']->id]);
        $secDbOps->update(['section_head_employee_id' => $createdEmployees['EMP-0009']->id]);
        $secDigitalMkt->update(['section_head_employee_id' => $createdEmployees['EMP-0010']->id]);
        $secCampaigns->update(['section_head_employee_id' => $createdEmployees['EMP-0011']->id]);
        $secPr->update(['section_head_employee_id' => $createdEmployees['EMP-0012']->id]);

        // 7) صلاحيات كل موديولات النظام (EMS Modules) وربطها بالأدوار بنطاقات مختلفة
        //    المفهوم: Role (دور) + Permission (ماذا يستطيع أن يفعل) + Scope (أين):
        //    self=بياناته فقط / department=دائرته أو قسمه / organization=كل المؤسسة
        $modulesWithActions = [
            'employees' => ['view', 'create', 'update', 'delete'],
            'departments' => ['view', 'create', 'update', 'delete'],
            'projects' => ['view', 'create', 'update', 'delete'],
            'tasks' => ['view', 'create', 'update', 'delete'],
            'attendance' => ['view', 'create', 'update', 'delete'],
            'performance' => ['view', 'create', 'update', 'delete'],
            'reports' => ['view'],
            'users' => ['view', 'create', 'update', 'delete'],
            'settings' => ['view', 'update'],
        ];

        $permissionModels = [];
        foreach ($modulesWithActions as $module => $actions) {
            foreach ($actions as $action) {
                $key = "{$module}.{$action}";
                $permissionModels[$key] = Permission::firstOrCreate(
                    ['key' => $key],
                    ['display_name' => $this->permissionDisplayName($module, $action)]
                );
            }
        }
        // صلاحية خاصة: إدارة مصفوفة الصلاحيات نفسها (مين يعطي مين شو) - للسوبر أدمن فقط
        $permissionModels['settings.manage_permissions'] = Permission::firstOrCreate(
            ['key' => 'settings.manage_permissions'],
            ['display_name' => 'إدارة صلاحيات الأدوار والنطاقات']
        );

        // خريطة: اسم الدور => [مفتاح الصلاحية => نطاق التطبيق]
        $roleScopeMap = [
            'SA' => $this->allPermissionsAtScope(array_keys($permissionModels), 'organization'),

            'EX' => [
                'employees.view' => 'organization',
                'employees.update' => 'organization',
                'departments.view' => 'organization',
                'departments.update' => 'organization',
                'projects.view' => 'organization',
                'projects.update' => 'organization',
                'tasks.view' => 'organization',
                'tasks.update' => 'organization',
                'attendance.view' => 'organization',
                'attendance.update' => 'organization',
                'performance.view' => 'organization',
                'performance.update' => 'organization',
                'reports.view' => 'organization',
                'users.view' => 'organization',
                'users.update' => 'organization',
                'settings.view' => 'organization',
            ],

            'DP' => [
                'employees.view' => 'department',
                'employees.create' => 'department',
                'employees.update' => 'department',
                'departments.view' => 'department',
                'projects.view' => 'department',
                'projects.create' => 'department',
                'projects.update' => 'department',
                'tasks.view' => 'department',
                'tasks.create' => 'department',
                'tasks.update' => 'department',
                'attendance.view' => 'department',
                'attendance.update' => 'department',
                'performance.view' => 'department',
                'performance.create' => 'department',
                'performance.update' => 'department',
                'reports.view' => 'department',
                'users.view' => 'department',
            ],

            'DM' => [
                'employees.view' => 'department',
                'employees.update' => 'department',
                'projects.view' => 'department',
                'projects.update' => 'department',
                'tasks.view' => 'department',
                'tasks.update' => 'department',
                'attendance.view' => 'department',
                'performance.view' => 'department',
                'reports.view' => 'department',
                'users.view' => 'department',
            ],

            'SH' => [
                // ملاحظة: عمود scope فيه 3 قيم بس، وبنعامل "department" هون كحدود القسم (section) بالكود
                'employees.view' => 'department',
                'tasks.view' => 'department',
                'tasks.update' => 'department',
                'attendance.view' => 'department',
                'performance.view' => 'department',
            ],

            'EMP' => [
                'employees.view' => 'self',
                'tasks.view' => 'self',
                'tasks.update' => 'self',
                'attendance.view' => 'self',
                'performance.view' => 'self',
            ],

            'TA' => [
                'employees.view' => 'self',
                'tasks.view' => 'self',
                'tasks.update' => 'self',
                'attendance.view' => 'organization',
                // TA (مساعد إداري) هو المستخدم الافتراضي المخوّل يسجل حضور/انصراف كل الموظفين.
                // هاد بس مثال ابتدائي - السوبر أدمن يقدر يبدّله من مصفوفة الصلاحيات وقت ما بدو.
                'attendance.create' => 'organization',
                'performance.view' => 'self',
            ],
        ];

        foreach ($roleScopeMap as $roleName => $permsWithScope) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                continue;
            }
            foreach ($permsWithScope as $permKey => $scope) {
                if (! isset($permissionModels[$permKey])) {
                    continue;
                }
                $role->permissions()->syncWithoutDetaching([
                    $permissionModels[$permKey]->id => ['scope' => $scope],
                ]);
            }
        }
    }

    private function allPermissionsAtScope(array $permissionKeys, string $scope): array
    {
        return array_fill_keys($permissionKeys, $scope);
    }

    private function permissionDisplayName(string $module, string $action): string
    {
        $moduleLabels = [
            'employees' => 'الموظفين',
            'departments' => 'الدوائر',
            'projects' => 'المشاريع',
            'tasks' => 'المهام',
            'attendance' => 'الحضور',
            'performance' => 'الأداء',
            'reports' => 'التقارير',
            'users' => 'المستخدمين',
            'settings' => 'الإعدادات',
        ];
        $actionLabels = [
            'view' => 'عرض',
            'create' => 'إضافة',
            'update' => 'تعديل',
            'delete' => 'حذف',
        ];

        return ($actionLabels[$action] ?? $action) . ' ' . ($moduleLabels[$module] ?? $module);
    }
}
