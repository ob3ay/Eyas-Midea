@extends('layouts.portal')
@section('title', 'إدارة المستخدمين والصلاحيات')
@section('content')

    <div class="min-h-screen bg-slate-50 py-6" dir="rtl">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        إدارة المستخدمين والصلاحيات
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        إدارة حسابات المستخدمين والأدوار والصلاحيات
                    </p>
                </div>

                @if ($canCreate)
                    <button type="button" onclick="openModal('modal-add-user')"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        إضافة حساب مستخدم
                    </button>
                @endif
            </div>

            {{-- Alerts --}}
            @if (session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Statistics --}}
            @php
                $totalUsers = $users->count();
                $activeUsers = $users->where('is_active', true)->count();
                $inactiveUsers = $users->where('is_active', false)->count();
                $totalRoles = $roles->count();
                $totalPermissions = $permissions->count();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">إجمالي المستخدمين</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">
                                {{ $totalUsers }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5 text-slate-700"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">المستخدمون النشطون</p>
                            <p class="text-2xl font-bold text-emerald-600 mt-1">
                                {{ $activeUsers }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i data-lucide="user-check" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">الأدوار</p>
                            <p class="text-2xl font-bold text-indigo-600 mt-1">
                                {{ $totalRoles }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
                            <i data-lucide="shield" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500">الصلاحيات</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">
                                {{ $totalPermissions }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                            <i data-lucide="key-round" class="w-5 h-5 text-purple-600"></i>
                        </div>
                    </div>
                </div>

            </div>


            {{-- Super Admin --}}
            @if ($superAdmin)
                <div class="bg-gradient-to-l from-slate-900 to-slate-800 rounded-2xl p-5 mb-6 text-white">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        <div class="flex items-center gap-4">

                            @if ($superAdmin->employee?->avatar)
                                <img src="{{ asset('storage/' . $superAdmin->employee->avatar) }}"
                                    alt="{{ $superAdmin->employee?->full_name ?? $superAdmin->username }}"
                                    class="w-14 h-14 rounded-full object-cover border-2 border-white/20">
                            @else
                                <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                                    <i data-lucide="shield-check" class="w-7 h-7"></i>
                                </div>
                            @endif

                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-bold text-lg">
                                        {{ $superAdmin->employee?->full_name ?? $superAdmin->username }}
                                    </h2>

                                    <span class="px-2 py-1 rounded-full text-xs bg-amber-400/20 text-amber-300">
                                        Super Admin
                                    </span>
                                </div>

                                <p class="text-sm text-slate-300 mt-1">
                                    {{ $superAdmin->email }}
                                </p>

                                @if ($superAdmin->employee?->employee_code)
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $superAdmin->employee->employee_code }}
                                    </p>
                                @endif
                            </div>

                        </div>

                        <div class="text-sm text-slate-300">
                            <span class="font-semibold text-white">
                                {{ $superAdmin->roles->count() }}
                            </span>
                            أدوار مرتبطة
                        </div>

                    </div>

                </div>
            @endif


            {{-- Permissions Matrix --}}
            <div class="bg-white rounded-2xl border border-slate-200 mb-6 overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-200 flex justify-between">
                  
                  <div>
                    <h2 class="font-bold text-slate-800">
                        مصفوفة الصلاحيات
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        الصلاحيات المرتبطة بكل دور ونطاقها
                    </p>
                </div>
                <form action="">
                    <select class="bg-slate-800 text-xs rounded-lg  px-3 py-2  text-white" onchange="this.form.submit()">
                        <option value="">اختر دور</option>
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->id }}">{{ $permission->display_name ?: $permission->key }}</option>
                        @endforeach
                    </select>
                </form>
                  </div>


                @if ($roles->count() && $permissions->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-700 whitespace-nowrap">
                                        الصلاحية
                                    </th>

                                    @foreach ($roles as $role)
                                        <th class="px-4 py-3 text-center font-semibold text-slate-700 whitespace-nowrap">
                                            {{ $role->display_name ?: $role->name }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @foreach ($permissions as $permission)
                                    <tr class="hover:bg-slate-50">

                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-800">
                                                {{ $permission->display_name ?: $permission->key }}
                                            </div>

                                            @if ($permission->description)
                                                <div class="text-xs text-slate-400 mt-1">
                                                    {{ $permission->description }}
                                                </div>
                                            @endif
                                        </td>

                                        @foreach ($roles as $role)
                                            @php
                                                $rolePermission = $role->permissions->firstWhere('id', $permission->id);

                                                $scope = $rolePermission?->pivot?->scope;

                                                $scopeLabels = [
                                                    'organization' => 'المؤسسة',
                                                    'department' => 'الدائرة',
                                                    'self' => 'ذاتي',
                                                ];

                                                $scopeClasses = [
                                                    'organization' => 'bg-emerald-100 text-emerald-700',
                                                    'department' => 'bg-blue-100 text-blue-700',
                                                    'self' => 'bg-amber-100 text-amber-700',
                                                ];
                                            @endphp

                                            <td class="px-4 py-3 text-center">

                                                @if ($canManagePermissions)
                                                    <form method="POST"
                                                        action="{{ route('settings.roles.permissions.update', ['role' => $role, 'permission' => $permission]) }}"
                                                        onsubmit="return true;">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="scope" onchange="this.form.submit()"
                                                            class="text-xs rounded-lg border-slate-200 px-2 py-1 {{ $scopeClasses[$scope] ?? 'bg-slate-50 text-slate-500' }}">
                                                            <option value="" {{ !$scope ? 'selected' : '' }}>— بدون
                                                                —</option>
                                                            <option value="self"
                                                                {{ $scope === 'self' ? 'selected' : '' }}>ذاتي (Self)
                                                            </option>
                                                            <option value="department"
                                                                {{ $scope === 'department' ? 'selected' : '' }}>
                                                                الدائرة/القسم</option>
                                                            <option value="organization"
                                                                {{ $scope === 'organization' ? 'selected' : '' }}>المؤسسة
                                                                كاملة</option>
                                                        </select>
                                                    </form>
                                                @else
                                                    @if ($scope)
                                                        <span
                                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $scopeClasses[$scope] ?? 'bg-slate-100 text-slate-700' }}">
                                                            {{ $scopeLabels[$scope] ?? $scope }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-300">
                                                            —
                                                        </span>
                                                    @endif
                                                @endif

                                            </td>
                                        @endforeach

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>
                @else
                    <div class="p-10 text-center text-slate-500">
                        لا توجد أدوار أو صلاحيات مسجلة حالياً.
                    </div>
                @endif

            </div>


            {{-- Users Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

                <div
                    class="px-5 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    <div>
                        <h2 class="font-bold text-slate-800">
                            حسابات المستخدمين
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            جميع حسابات النظام المسجلة
                        </p>
                    </div>

                    <div class="relative">
                        <i data-lucide="search"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>

                        <input type="text" id="userSearch" placeholder="بحث..."
                            class="pr-9 pl-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-slate-300 focus:border-slate-400 outline-none">
                    </div>

                </div>


                @if ($users->count())
                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead class="bg-slate-50 border-b border-slate-200">

                                <tr>

                                    <th class="px-4 py-3 text-right font-semibold text-slate-700">
                                        المستخدم
                                    </th>

                                    <th class="px-4 py-3 text-right font-semibold text-slate-700">
                                        البريد
                                    </th>

                                    <th class="px-4 py-3 text-right font-semibold text-slate-700">
                                        الأدوار
                                    </th>

                                    <th class="px-4 py-3 text-center font-semibold text-slate-700">
                                        النطاق
                                    </th>

                                    <th class="px-4 py-3 text-center font-semibold text-slate-700">
                                        الحالة
                                    </th>

                                    <th class="px-4 py-3 text-center font-semibold text-slate-700">
                                        آخر دخول
                                    </th>

                                    <th class="px-4 py-3 text-center font-semibold text-slate-700">
                                        الإجراءات
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="usersTableBody" class="divide-y divide-slate-100">

                                @foreach ($users as $user)
                                    @php

                                        $highestScope = null;

                                        foreach ($user->roles as $role) {
                                            foreach ($role->permissions as $permission) {
                                                $currentScope = $permission->pivot->scope ?? null;

                                                if (!$currentScope) {
                                                    continue;
                                                }

                                                $priority = [
                                                    'organization' => 3,
                                                    'department' => 2,
                                                    'self' => 1,
                                                ];

                                                if (
                                                    $highestScope === null ||
                                                    ($priority[$currentScope] ?? 0) > ($priority[$highestScope] ?? 0)
                                                ) {
                                                    $highestScope = $currentScope;
                                                }
                                            }
                                        }

                                        $scopeLabels = [
                                            'organization' => 'المؤسسة',
                                            'department' => 'الدائرة',
                                            'self' => 'ذاتي',
                                        ];

                                        $scopeClasses = [
                                            'organization' => 'bg-emerald-100 text-emerald-700',
                                            'department' => 'bg-blue-100 text-blue-700',
                                            'self' => 'bg-amber-100 text-amber-700',
                                        ];

                                        $searchText = strtolower(
                                            ($user->username ?? '') .
                                                ' ' .
                                                ($user->email ?? '') .
                                                ' ' .
                                                ($user->employee?->full_name ?? '') .
                                                ' ' .
                                                ($user->employee?->employee_code ?? ''),
                                        );
                                    @endphp


                                    <tr class="user-row hover:bg-slate-50" data-search="{{ $searchText }}">

                                        {{-- User --}}
                                        <td class="px-4 py-4">

                                            <div class="flex items-center gap-3">

                                                @if ($user->employee?->avatar)
                                                    <img src="{{ asset('storage/' . $user->employee->avatar) }}"
                                                        alt="{{ $user->employee?->full_name ?? $user->username }}"
                                                        class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                                                        <i data-lucide="user" class="w-5 h-5 text-slate-500"></i>
                                                    </div>
                                                @endif

                                                <div>

                                                    <div class="font-semibold text-slate-800">
                                                        {{ $user->employee?->full_name ?? $user->username }}
                                                    </div>

                                                    <div class="text-xs text-slate-400 mt-1">
                                                        {{ $user->username }}

                                                        @if ($user->employee?->employee_code)
                                                            · {{ $user->employee->employee_code }}
                                                        @endif
                                                    </div>

                                                </div>

                                            </div>

                                        </td>


                                        {{-- Email --}}
                                        <td class="px-4 py-4 text-slate-600">
                                            {{ $user->email }}
                                        </td>


                                        {{-- Roles --}}
                                        <td class="px-4 py-4">

                                            <div class="flex flex-wrap gap-1.5">

                                                @forelse($user->roles as $role)
                                                    <span
                                                        class="inline-flex px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">
                                                        {{ $role->display_name ?: $role->name }}
                                                    </span>

                                                @empty

                                                    <span class="text-slate-400 text-xs">
                                                        بدون دور
                                                    </span>
                                                @endforelse

                                            </div>

                                        </td>


                                        {{-- Scope --}}
                                        <td class="px-4 py-4 text-center">

                                            @if ($highestScope)
                                                <span
                                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $scopeClasses[$highestScope] ?? 'bg-slate-100 text-slate-700' }}">
                                                    {{ $scopeLabels[$highestScope] ?? $highestScope }}
                                                </span>
                                            @else
                                                <span class="text-slate-300">
                                                    —
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Status --}}
                                        <td class="px-4 py-4 text-center">

                                            @if ($user->is_active)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    نشط
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                    غير نشط
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Last Login --}}
                                        <td class="px-4 py-4 text-center text-slate-500">

                                            @if ($user->last_login_at)
                                                <div>
                                                    {{ $user->last_login_at->format('Y-m-d') }}
                                                </div>

                                                <div class="text-xs text-slate-400 mt-1">
                                                    {{ $user->last_login_at->format('H:i') }}
                                                </div>
                                            @else
                                                <span class="text-slate-400">
                                                    لم يسجل دخول
                                                </span>
                                            @endif

                                        </td>


                                        {{-- Actions --}}
                                        <td class="px-4 py-4">

                                            <div class="flex items-center justify-center gap-2">

                                                @if ($canUpdate)
                                                    <button type="button"
                                                        onclick="openEditUserModal({{ $user->id }})"
                                                        class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600"
                                                        title="تعديل">
                                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                                    </button>
                                                @endif


                                                @if ($canDelete)
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="w-9 h-9 rounded-lg bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-600"
                                                            title="حذف">
                                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                        </button>

                                                    </form>
                                                @endif

                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>
                @else
                    <div class="p-12 text-center">

                        <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center">
                            <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                        </div>

                        <h3 class="mt-4 font-semibold text-slate-700">
                            لا يوجد مستخدمون
                        </h3>

                        <p class="text-sm text-slate-400 mt-1">
                            لم يتم إنشاء أي حساب مستخدم حتى الآن.
                        </p>

                    </div>
                @endif

            </div>

        </div>


        {{-- Add User Modal --}}
        <div id="modal-add-user" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">

                    <div>
                        <h2 class="font-bold text-lg text-slate-800">
                            إضافة حساب مستخدم
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            إنشاء حساب وربطه بموظف وأدوار
                        </p>
                    </div>

                    <button type="button" onclick="closeModal('modal-add-user')"
                        class="w-9 h-9 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>

                </div>


                <form method="POST" action="{{ route('users.store') }}" class="p-6">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Employee --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                الموظف
                            </label>

                            <select name="employee_id" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">
                                <option value="">اختر الموظف</option>

                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }}
                                        @if ($employee->employee_code)
                                            — {{ $employee->employee_code }}
                                        @endif
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- Username --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                اسم المستخدم
                            </label>

                            <input type="text" name="username" required maxlength="100"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300"
                                placeholder="username">

                        </div>


                        {{-- Email --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                البريد الإلكتروني
                            </label>

                            <input type="email" name="email" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300"
                                placeholder="email@example.com">

                        </div>


                        {{-- Password --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                كلمة المرور
                            </label>

                            <input type="password" name="password" required minlength="8"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">

                        </div>


                        {{-- Password Confirmation --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                تأكيد كلمة المرور
                            </label>

                            <input type="password" name="password_confirmation" required minlength="8"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">

                        </div>


                        {{-- Roles --}}
                        @if ($canManagePermissions)
                            <div class="md:col-span-2">

                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    الأدوار
                                </label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

                                    @forelse($roles as $role)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">

                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                class="rounded border-slate-300">

                                            <div>
                                                <div class="text-sm font-medium text-slate-700">
                                                    {{ $role->display_name ?: $role->name }}
                                                </div>

                                                @if ($role->description)
                                                    <div class="text-xs text-slate-400 mt-1">
                                                        {{ $role->description }}
                                                    </div>
                                                @endif
                                            </div>

                                        </label>

                                    @empty

                                        <p class="text-sm text-slate-400">
                                            لا توجد أدوار مسجلة.
                                        </p>
                                    @endforelse

                                </div>

                            </div>
                        @endif


                        {{-- Active --}}
                        <div class="md:col-span-2">

                            <label class="flex items-center gap-3 cursor-pointer">

                                <input type="checkbox" name="is_active" value="1" checked
                                    class="rounded border-slate-300">

                                <span class="text-sm font-medium text-slate-700">
                                    الحساب نشط
                                </span>

                            </label>

                        </div>

                    </div>


                    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-slate-200">

                        <button type="button" onclick="closeModal('modal-add-user')"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                            إلغاء
                        </button>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
                            إنشاء الحساب
                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- Edit User Modal --}}
        <div id="modal-edit-user" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">

                    <div>
                        <h2 class="font-bold text-lg text-slate-800">
                            تعديل حساب المستخدم
                        </h2>
                    </div>

                    <button type="button" onclick="closeModal('modal-edit-user')"
                        class="w-9 h-9 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>

                </div>


                <form id="editUserForm" method="POST" class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Employee --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                الموظف
                            </label>

                            <select name="employee_id" id="edit_employee_id" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">

                                @foreach ($users as $existingUser)
                                    @if ($existingUser->employee)
                                        <option value="{{ $existingUser->employee->id }}">
                                            {{ $existingUser->employee->full_name }}
                                            @if ($existingUser->employee->employee_code)
                                                — {{ $existingUser->employee->employee_code }}
                                            @endif
                                        </option>
                                    @endif
                                @endforeach

                            </select>

                        </div>


                        {{-- Username --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                اسم المستخدم
                            </label>

                            <input type="text" name="username" id="edit_username" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">

                        </div>


                        {{-- Email --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                البريد الإلكتروني
                            </label>

                            <input type="email" name="email" id="edit_email" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">

                        </div>


                        {{-- Password --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                كلمة المرور الجديدة
                            </label>

                            <input type="password" name="password" minlength="8"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300"
                                placeholder="اتركها فارغة للإبقاء على الحالية">

                        </div>


                        {{-- Confirm Password --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                تأكيد كلمة المرور
                            </label>

                            <input type="password" name="password_confirmation" minlength="8"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:ring-2 focus:ring-slate-300">

                        </div>


                        {{-- Roles --}}
                        @if ($canManagePermissions)
                            <div class="md:col-span-2">

                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    الأدوار
                                </label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

                                    @foreach ($roles as $role)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">

                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                class="edit-role rounded border-slate-300">

                                            <span class="text-sm font-medium text-slate-700">
                                                {{ $role->display_name ?: $role->name }}
                                            </span>

                                        </label>
                                    @endforeach

                                </div>

                            </div>
                        @endif


                        {{-- Active --}}
                        <div class="md:col-span-2">

                            <label class="flex items-center gap-3 cursor-pointer">

                                <input type="checkbox" name="is_active" value="1" id="edit_is_active"
                                    class="rounded border-slate-300">

                                <span class="text-sm font-medium text-slate-700">
                                    الحساب نشط
                                </span>

                            </label>

                        </div>

                    </div>


                    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-slate-200">

                        <button type="button" onclick="closeModal('modal-edit-user')"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                            إلغاء
                        </button>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
                            حفظ التعديلات
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
        const usersData = @js(
    $users
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'employee_id' => $user->employee_id,
                'username' => $user->username,
                'email' => $user->email,
                'is_active' => (bool) $user->is_active,
                'roles' => $user->roles->pluck('id')->values()->all(),
            ];
        })
        ->values()
        ->all(),
);

        function openModal(id) {
            const modal = document.getElementById(id);

            if (!modal) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function openEditUserModal(userId) {
            const user = usersData.find(
                item => Number(item.id) === Number(userId)
            );

            if (!user) {
                return;
            }

            const form = document.getElementById('editUserForm');

            if (form) {
                form.action = "{{ url('/users') }}/" + user.id;
            }

            const employeeInput = document.getElementById('edit_employee_id');
            const usernameInput = document.getElementById('edit_username');
            const emailInput = document.getElementById('edit_email');
            const activeInput = document.getElementById('edit_is_active');

            if (employeeInput) {
                employeeInput.value = user.employee_id ?? '';
            }

            if (usernameInput) {
                usernameInput.value = user.username ?? '';
            }

            if (emailInput) {
                emailInput.value = user.email ?? '';
            }

            if (activeInput) {
                activeInput.checked = user.is_active;
            }

            document.querySelectorAll('.edit-role').forEach(function(checkbox) {
                checkbox.checked = user.roles.includes(
                    Number(checkbox.value)
                );
            });

            openModal('modal-edit-user');
        }

        const searchInput = document.getElementById('userSearch');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const value = this.value.trim().toLowerCase();

                document.querySelectorAll('.user-row').forEach(function(row) {
                    const text = row.dataset.search || '';

                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        }

        document.querySelectorAll('[id^="modal-"]').forEach(function(modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('[id^="modal-"]').forEach(function(modal) {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal.id);
                }
            });
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>

@endsection
