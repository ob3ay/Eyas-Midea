<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('dashboard');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [LoginController::class, 'showLoginForm']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->name('login.post');
});


Route::post(
    '/logout',
    [LoginController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Protected System
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::prefix('employees')
        ->name('employees.')
        ->group(function () {

            Route::get(
                '/',
                [EmployeeController::class, 'index']
            )->name('index');

            Route::post(
                '/store',
                [EmployeeController::class, 'store']
            )->name('store');

            Route::put(
                '/{employee}',
                [EmployeeController::class, 'update']
            )->name('update');

            Route::delete(
                '/{employee}',
                [EmployeeController::class, 'destroy']
            )->name('destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    Route::prefix('departments')
        ->name('departments.')
        ->group(function () {

            Route::get(
                '/',
                [DepartmentController::class, 'index']
            )->name('index');

            Route::post(
                '/store',
                [DepartmentController::class, 'store']
            )->name('store');

            Route::put(
                '/{department}',
                [DepartmentController::class, 'update']
            )->name('update');

            Route::delete(
                '/{department}',
                [DepartmentController::class, 'destroy']
            )->name('destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */

    Route::prefix('projects')
        ->name('projects.')
        ->group(function () {

            Route::get(
                '/',
                [ProjectController::class, 'index']
            )->name('index');

            Route::post(
                '/',
                [ProjectController::class, 'store']
            )->name('store');

            Route::put(
                '/{project}',
                [ProjectController::class, 'update']
            )->name('update');

            Route::delete(
                '/{project}',
                [ProjectController::class, 'destroy']
            )->name('destroy');
        });


    /*
    |--------------------------------------------------------------------------
    | Tasks
    |--------------------------------------------------------------------------
    */

    Route::prefix('tasks')
        ->name('tasks.')
        ->group(function () {

            Route::get(
                '/',
                [TaskController::class, 'index']
            )->name('index');

            Route::post(
                '/',
                [TaskController::class, 'store']
            )->name('store');

            Route::put(
                '/{task}',
                [TaskController::class, 'update']
            )->name('update');

            Route::delete(
                '/{task}',
                [TaskController::class, 'destroy']
            )->name('destroy');

            Route::post(
                '/{task}/state',
                [TaskController::class, 'updateState']
            )->name('update-state');
        });


    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    Route::prefix('attendance')
        ->name('attendance.')
        ->group(function () {

            Route::get(
                '/',
                [AttendanceController::class, 'index']
            )->name('index');

            Route::post(
                '/check-in',
                [AttendanceController::class, 'checkIn']
            )->name('checkin');

            Route::post(
                '/check-out',
                [AttendanceController::class, 'checkOut']
            )->name('checkout');

            Route::post(
                '/manual-log',
                [AttendanceController::class, 'manual']
            )->name('manual');
        });


    /*
    |--------------------------------------------------------------------------
    | Performance
    |--------------------------------------------------------------------------
    */

    Route::prefix('performance')
        ->name('performance.')
        ->group(function () {

            Route::get(
                '/',
                [PerformanceController::class, 'index']
            )->name('index');

            Route::post(
                '/evaluate',
                [PerformanceController::class, 'evaluate']
            )->name('evaluate');

            Route::put(
                '/{evaluation}',
                [PerformanceController::class, 'update']
            )->name('update');
        });


    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::prefix('users')
        ->name('users.')
        ->group(function () {

            Route::get(
                '/',
                [UserController::class, 'index']
            )->name('index');

            Route::post(
                '/',
                [UserController::class, 'store']
            )->name('store');

            Route::put(
                '/{user}/role',
                [UserController::class, 'updateRole']
            )->name('update-role');

            Route::patch(
                '/{user}/toggle',
                [UserController::class, 'toggleActive']
            )->name('toggle-active');
        });
});
});
