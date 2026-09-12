<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - نظام مجموعة إياس الإعلامية (EMS)
|--------------------------------------------------------------------------
|
| كافة المسارات والدوال المهيأة للربط مع Controllers ونماذج إطار العمل Laravel 13.
|
*/

// مسارات المصادقة وتسجيل الدخول
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// مسارات البوابة المحمية بالصلاحيات (Auth Middleware)
// ملاحظة: كانت هاي المجموعة سابقاً بدون middleware('auth') فعلي، يعني أي زائر
// كان يقدر يوصل لكل الصفحات بدون تسجيل دخول. تمت إضافة الحماية الحقيقية هون.
Route::middleware(['web', 'auth'])->group(function () {

    // 0. لوحة التحكم التنفيذية الشاملة
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. شؤون الموظفين (Employees)
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::post('/store', [EmployeeController::class, 'store'])->name('store');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });
    // 2. الدوائر والأقسام (Departments & Sections)
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/structure', function () {
            return view('departments.structure');
        })->name('structure');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
    });

    Route::post('/sections', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'section_head_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);
        \App\Models\Section::create($data);
        return back()->with('success', 'تمت إضافة القسم التخصصي بنجاح');
    })->name('sections.store');

    Route::put('/sections/{section}', function (\Illuminate\Http\Request $request, \App\Models\Section $section) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'section_head_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);
        $section->update($data);
        return back()->with('success', 'تم تحديث بيانات القسم بنجاح');
    })->name('sections.update');

    Route::delete('/sections/{section}', function (\App\Models\Section $section) {
        if ($section->employees()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا القسم نظراً لوجود موظفين مسجلين به.');
        }
        $section->delete();
        return back()->with('success', 'تم حذف القسم بنجاح');
    })->name('sections.destroy');

    // 3. المشاريع والإنتاج (Projects)
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', function () {
            return view('projects.index');
        })->name('index');
        Route::post('/store', function () {
            return back()->with('success', 'تم حفظ المشروع بنجاح');
        })->name('store');
        Route::put('/{id}', function ($id) {
            return back()->with('success', 'تم تحديث المشروع');
        })->name('update');
    });

    // 4. المهام وتتبع الحالات الخمس (Tasks & 5-Stages Lifecycle)
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', function () {
            return view('tasks.index');
        })->name('index');
        Route::post('/store', function () {
            return back()->with('success', 'تم إسناد المهمة بنجاح');
        })->name('store');
        Route::post('/{id}/state', function ($id) {
            return response()->json(['status' => 'updated']);
        })->name('update-state');
    });

    // 5. الحضور والانصراف والبصمة (Attendance)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', function () {
            return view('attendance.index');
        })->name('index');
        Route::post('/check-in', function () {
            return back()->with('success', 'تم تسجيل الحضور');
        })->name('checkin');
        Route::post('/check-out', function () {
            return back()->with('success', 'تم تسجيل الانصراف');
        })->name('checkout');
        Route::post('/manual-log', function () {
            return back()->with('success', 'تم القيد اليدوي');
        })->name('manual');
    });

    // 6. الأداء والإنجاز المؤسسي (Performance KPIs)
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', function () {
            return view('performance.index');
        })->name('index');
        Route::post('/evaluate', function () {
            return back()->with('success', 'تم اعتماد تقييم الأداء');
        })->name('evaluate');
    });

    // 7. المستخدمون والصلاحيات (Users, Roles & Scopes)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', function () {
            return view('users.index');
        })->name('index');
        Route::post('/store', function () {
            return back()->with('success', 'تم إنشاء الحساب بنجاح');
        })->name('store');
        Route::put('/{id}/role', function ($id) {
            return back()->with('success', 'تم تحديث الصلاحيات');
        })->name('update-role');
    });
});
