<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::post('/store', [EmployeeController::class, 'store'])->name('store');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

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

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::post('/store', [ProjectController::class, 'store'])->name('store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/store', [TaskController::class, 'store'])->name('store');
        Route::post('/{task}/state', [TaskController::class, 'updateState'])->name('update-state');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });

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

    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', function () {
            return view('performance.index');
        })->name('index');
        Route::post('/evaluate', function () {
            return back()->with('success', 'تم اعتماد تقييم الأداء');
        })->name('evaluate');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::put('/{targetUser}', [UserController::class, 'update'])->name('update');
        Route::delete('/{targetUser}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::put('/settings/roles/{role}/permissions/{permission}', [RolePermissionController::class, 'update'])
        ->name('settings.roles.permissions.update');
});
