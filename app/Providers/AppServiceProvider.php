<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Section;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use App\Policies\SectionPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Section::class, SectionPolicy::class);
        RateLimiter::for('login', function (Request $request): array {
            $key = Str::transliterate(
                Str::lower((string) $request->input('email')).'|'.$request->ip()
            );

            return [Limit::perMinute(5)->by($key)];
        });

        View::composer('layouts.portal', function ($view): void {
            $user = auth()->user();
            $view->with('notificationsUnreadCount', $user?->notifications()->unread()->count() ?? 0);
        });

        Gate::define('view-departments', fn (User $user): bool => $user->can('viewAny', Department::class));
        Gate::define('create-department', fn (User $user): bool => $user->can('create', Department::class));
        Gate::define('edit-department', function (User $user, ?Department $department = null): bool {
            return $department === null
                ? $user->hasPermission('departments.update')
                : $user->can('update', $department);
        });
        Gate::define('delete-department', function (User $user, ?Department $department = null): bool {
            return $department === null
                ? $user->hasPermission('departments.delete')
                : $user->can('delete', $department);
        });
        Gate::define('view-sections', fn (User $user): bool => $user->hasPermission('departments.view'));
        Gate::define('create-section', fn (User $user): bool => $user->can('create', Section::class));
        Gate::define('edit-section', function (User $user, ?Section $section = null): bool {
            return $section === null
                ? $user->hasPermission('departments.update')
                : $user->can('update', $section);
        });
        Gate::define('delete-section', function (User $user, ?Section $section = null): bool {
            return $section === null
                ? $user->hasPermission('departments.delete')
                : $user->can('delete', $section);
        });
    }
}
