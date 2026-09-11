<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 1. شؤون الموظفين (Employees)
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', function () {
            return view('employees.index');
        })->name('index');
        Route::post('/store', function () {
            return back()->with('success', 'تم حفظ بيانات الموظف بنجاح');
        })->name('store');
        Route::put('/{id}', function ($id) {
            return back()->with('success', 'تم تحديث الموظف');
        })->name('update');
        Route::delete('/{id}', function ($id) {
            return back()->with('success', 'تم حذف الموظف');
        })->name('destroy');
    });

    // 2. الدوائر والأقسام (Departments & Sections)
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
    });

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
