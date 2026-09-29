<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
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

Route::middleware(['web', 'auth', 'active.user'])->group(function () {

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
        Route::get('/structure', [DepartmentController::class, 'structure'])->name('structure');
        Route::get('/show/{department}', [DepartmentController::class, 'show'])->name('show');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/employees/{employee}/assign', [DepartmentController::class, 'assignEmployee'])->name('employees.assign');
    });

    Route::post('/sections', [DepartmentController::class, 'storeSection'])->name('sections.store');
    Route::get('/sections/{section}', [DepartmentController::class, 'showSection'])->name('sections.show');
    Route::put('/sections/{section}', [DepartmentController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{section}', [DepartmentController::class, 'destroySection'])->name('sections.destroy');

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::post('/store', [ProjectController::class, 'store'])->name('store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
        Route::get('/attachments/{attachment}/download', [ProjectController::class, 'downloadAttachment'])->name('attachments.download');
        Route::delete('/attachments/{attachment}', [ProjectController::class, 'destroyAttachment'])->name('attachments.destroy');
    });

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/store', [TaskController::class, 'store'])->name('store');
        Route::post('/{task}/state', [TaskController::class, 'updateState'])->name('update-state');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/print', [AttendanceController::class, 'printSheet'])->name('print');
        Route::get('/export', [AttendanceController::class, 'exportCsv'])->name('export');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('checkout');
        Route::post('/manual-log', [AttendanceController::class, 'manualLog'])->name('manual');
        Route::put('/{attendanceRecord}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{attendanceRecord}', [AttendanceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('index');
        Route::post('/evaluate', [PerformanceController::class, 'evaluate'])->name('evaluate');
    });

    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/', [EmployeeRequestController::class, 'index'])->name('index');
        Route::post('/', [EmployeeRequestController::class, 'store'])->name('store');
        Route::post('/{employeeRequest}/cancel', [EmployeeRequestController::class, 'cancel'])->name('cancel');
        Route::post('/{employeeRequest}/approve', [EmployeeRequestController::class, 'approve'])->name('approve');
        Route::post('/{employeeRequest}/reject', [EmployeeRequestController::class, 'reject'])->name('reject');
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::put('/{targetUser}', [UserController::class, 'update'])->name('update');
        Route::patch('/{targetUser}/role', [UserController::class, 'updateRole'])->name('role.update');
        Route::delete('/{targetUser}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::put('/settings/roles/{role}/permissions/{permission}', [RolePermissionController::class, 'update'])
        ->name('settings.roles.permissions.update');
});
