<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | مجموعة إياس الإعلامية - EMS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}?v=1.2">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Google Cairo & Tajawal Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js for Search Modal & Interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    @stack('styles')
</head>

<body>

    <div class="ems-app">

        <!-- القائمة الجانبية (Sidebar) -->
        <aside class="ems-sidebar">
            <div class="ems-brand">
                <a href="{{ route('dashboard') }}" class="ems-brand-logo-wrap">
                    <img src="{{ asset('assets/images/logo-gold.png') }}" alt="مجموعة إياس الإعلامية"
                        class="ems-brand-logo"
                        onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23C5A059\'><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'">
                </a>
                <div class="ems-brand-title">مجموعة إياس الإعلامية</div>
                <div class="ems-brand-subtitle">EYAS Media System</div>
            </div>

            <nav class="ems-nav">
                <div class="ems-nav-group-title">لوحة التحكم والعمليات</div>
                <a href="{{ route('dashboard') }}"
                    class="ems-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="ems-nav-link-content">
                        <svg class="ems-nav-icon" viewBox="0 0 24 24">
                            <rect width="7" height="7" x="3" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="3" rx="1" />
                            <rect width="7" height="7" x="14" y="14" rx="1" />
                            <rect width="7" height="7" x="3" y="14" rx="1" />
                        </svg>
                        <span>لوحة التحكم الرئيسية</span>
                    </div>
                </a>

                <div class="ems-nav-group-title">الإدارة والتشغيل</div>

                @if (Route::has('employees.index'))
                    <a href="{{ route('employees.index') }}"
                        class="ems-nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            <span>شؤون الموظفين</span>
                        </div>
                        @if (isset($employeesCount))
                            <span class="ems-nav-badge">{{ $employeesCount }}</span>
                        @endif
                    </a>
                @endif

                @if (Route::has('departments.index'))
                    <a href="{{ route('departments.index') }}"
                        class="ems-nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path d="M3 21h18" />
                                <path d="M3 7v14" />
                                <path d="M21 7v14" />
                                <path d="M6 3h12l3 4H3z" />
                                <path d="M9 10h6" />
                                <path d="M9 14h6" />
                                <path d="M9 18h6" />
                            </svg>
                            <span>الدوائر والأقسام</span>
                        </div>
                        @if (isset($departmentsCount))
                            <span class="ems-nav-badge">{{ $departmentsCount }}</span>
                        @endif
                    </a>
                @endif

                @if (Route::has('projects.index'))
                    <a href="{{ route('projects.index') }}"
                        class="ems-nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path
                                    d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                            </svg>
                            <span>المشاريع والإنتاج</span>
                        </div>
                        @if (isset($projectsCount))
                            <span class="ems-nav-badge">{{ $projectsCount }}</span>
                        @endif
                    </a>
                @endif

                @if (Route::has('tasks.index'))
                    <a href="{{ route('tasks.index') }}"
                        class="ems-nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path d="m9 11 3 3L22 4" />
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                            </svg>
                            <span>المهام وتتبع الحالات</span>
                        </div>
                        @if (isset($tasksCount))
                            <span class="ems-nav-badge">{{ $tasksCount }}</span>
                        @endif
                    </a>
                @endif

                <div class="ems-nav-group-title">المتابعة والرقابة</div>

                @if (Route::has('attendance.index'))
                    <a href="{{ route('attendance.index') }}"
                        class="ems-nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span>الحضور والانصراف</span>
                        </div>
                    </a>
                @endif

                @if (Route::has('performance.index'))
                    <a href="{{ route('performance.index') }}"
                        class="ems-nav-link {{ request()->routeIs('performance.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path d="M12 20V10" />
                                <path d="M18 20V4" />
                                <path d="M6 20v-4" />
                            </svg>
                            <span>الأداء والإنجاز</span>
                        </div>
                    </a>
                @endif
                @if (Route::has('reports.index'))
                    <a href="{{ route('reports.index') }}"
                        class="ems-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path d="M3 3h18v18H3z" />
                                <path d="M3 9h18" />
                                <path d="M9 21V9" />
                            </svg>
                            <span>التقارير التنفيذية</span>
                        </div>
                    </a>
                @endif

                @if (Route::has('users.index'))
                    <a href="{{ route('users.index') }}"
                        class="ems-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <div class="ems-nav-link-content">
                            <svg class="ems-nav-icon" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span>المستخدمون والصلاحيات</span>
                        </div>
                        <span class="ems-badge badge-gold" style="font-size: 10px;">Full Scope</span>
                    </a>
                @endif
            </nav>

           

            <div class="ems-sidebar-footer">
                <span>مجموعة إياس الإعلامية</span>
                <span style="font-weight: 700; color: var(--ems-gold-hover);">EMS</span>
            </div>
        </aside>

        <!-- المحتوى الرئيسي -->
        <main class="ems-main">
            <header class="ems-topbar">
                <div class="ems-topbar-start">
                    <button class="ems-mobile-toggle" onclick="toggleSidebar()" aria-label="تبديل القائمة">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <line x1="3" y1="18" x2="21" y2="18" />
                        </svg>
                    </button>
                    <div class="ems-search-box">
                        <svg class="ems-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" class="ems-search-input" placeholder="بحث عام في المنظومة...">
                    </div>
                </div>

                <div class="ems-topbar-end">
                    <button class="ems-icon-btn" onclick="openModal('modal-quick-attendance')" title="تسجيل الحضور">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </button>

                    <button class="ems-icon-btn" title="الإشعارات">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <span class="badge-dot"></span>
                    </button>
                    <!-- بيانات المستخدم الحركية -->
                    @php
                        $authUser = auth()->user();

                        // اسم المستخدم من جدول users
                        $userName = $authUser?->username ?? 'المستخدم';

                        // جلب جميع الـ Roles الخاصة بالمستخدم
                        $userRoles = $authUser?->roleNames() ?? [];

                        // عرض أول Role بجانب اسم المستخدم
                        $userRole = $userRoles[0] ?? 'مستخدم';

                        // أول حرف من اسم المستخدم
                        $firstLetter = mb_substr($userName, 0, 1, 'UTF-8');
                    @endphp

                    @if ($authUser)
                        <div class="ems-user-profile" data-dropdown-toggle="userDropdownMenu">

                            <!-- Avatar -->
                            <div class="ems-avatar">
                                {{ $firstLetter }}
                            </div>

                            <!-- User Info -->
                            <div class="ems-user-info">
                                <span class="ems-user-name">
                                    {{ $userName }}
                                </span>

                                <span class="ems-user-role">
                                    {{ $userRole }}
                                </span>
                            </div>

                            <!-- Arrow -->
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>


                            <!-- Dropdown -->
                            <div class="ems-dropdown-menu" id="userDropdownMenu">

                                <!-- Profile -->
                                @if (Route::has('employees.show') && $authUser->employee)
                                    <a href="{{ route('employees.show', $authUser->employee->id) }}"
                                        class="ems-dropdown-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>

                                        <span>الملف الشخصي</span>
                                    </a>
                                @endif


                                <!-- Roles -->
                                @if (count($userRoles) > 0)

                                    <div class="ems-dropdown-item"
                                        style="cursor: default; flex-direction: column; align-items: flex-start; gap: 6px;">

                                        <div style="display: flex; align-items: center; gap: 8px;">

                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                            </svg>

                                            <span>الأدوار والصلاحيات</span>

                                        </div>

                                        <!-- جميع Roles -->
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px; padding-right: 24px;">

                                            @foreach ($userRoles as $role)
                                                <span
                                                    style="
                                    background: #fdf6e2;
                                    color: #9b7b32;
                                    padding: 3px 7px;
                                    border-radius: 5px;
                                    font-size: 10px;
                                    font-weight: 600;
                                ">
                                                    {{ $role }}
                                                </span>
                                            @endforeach

                                        </div>

                                    </div>

                                @endif


                                <div class="ems-dropdown-divider"></div>


                                <!-- Logout -->
                                @if (Route::has('logout'))
                                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                        @csrf

                                        <button type="submit" class="ems-dropdown-item"
                                            style="
                            color: #ef4444;
                            width: 100%;
                            text-align: right;
                            background: none;
                            border: none;
                            cursor: pointer;
                        ">

                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                                <polyline points="16 17 21 12 16 7" />
                                                <line x1="21" y1="12" x2="9" y2="12" />
                                            </svg>

                                            <span>تسجيل الخروج</span>

                                        </button>
                                    </form>
                                @endif

                            </div>

                        </div>
                    @endif


                </div>
            </header>

            <!-- محتوى الصفحات الفرعية -->
            <div class="ems-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- نافذة البصمة السريعة المشتركة -->
    <div class="ems-modal-overlay" id="modal-quick-attendance">
        <div class="ems-modal" style="max-width: 440px;">
            <div class="ems-modal-header">
                <div class="ems-modal-title">تسجيل بصمة الدوام</div>
                <button class="ems-modal-close" onclick="closeModal('modal-quick-attendance')">✕</button>
            </div>
            <div class="ems-modal-body" style="text-align: center; padding: 30px;">
                <div
                    style="width: 70px; height: 70px; border-radius: 50%; background: var(--ems-gold-light); border: 2px dashed var(--ems-gold); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--ems-gold-hover);">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">{{ $userName }}</h3>
                <p style="font-size: 12.5px; color: var(--ems-text-muted); margin-bottom: 16px;">{{ $userRole }} •
                    مجموعة إياس الإعلامية</p>
                <div class="ems-live-clock"
                    style="font-size: 24px; font-weight: 800; font-family: var(--font-mono); color: var(--ems-gold-hover); margin-bottom: 20px;">
                    --:--:--</div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button class="ems-btn ems-btn-primary" onclick="quickAttendanceAction()">
                        <span>تسجيل حضور (Check-In)</span>
                    </button>
                    <button class="ems-btn ems-btn-secondary" onclick="quickAttendanceAction()">
                        <span>تسجيل انصراف (Check-Out)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="ems-toast-container"></div>
    <script src="{{ asset('assets/js/portal.js') }}"></script>
    @stack('scripts')
</body>

</html>
