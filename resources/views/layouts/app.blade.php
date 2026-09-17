<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | مجموعة إياس الإعلامية</title>

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

<body class="bg-[#f5f7fb] text-slate-800 antialiased min-h-screen flex flex-col" x-data="{ searchOpen: false, searchQuery: '', searchResults: [] }"
    @keydown.window.prevent.cmd.k="searchOpen = true" @keydown.window.prevent.ctrl.k="searchOpen = true">
    <div class="flex flex-1 min-h-screen">

        <!-- RIGHT SIDEBAR (Arabic RTL Navigation) -->
        <aside
            class="w-64 bg-[#0b1329] text-slate-300 flex flex-col justify-between shrink-0 shadow-xl border-l border-slate-800/80 z-40">
            <div>
                <!-- Centered Logo on Top above Title -->
                <a href="{{ route('dashboard') }}"
                    class="p-6 flex flex-col items-center justify-center text-center border-b border-slate-800/80 hover:bg-slate-900/40 transition">
                    <img src="{{ asset('assets/images/logo-gold.png') }}" alt="EYAS Media Group"
                        class="h-12 w-auto object-contain mb-3 drop-shadow">
                    <div class="font-bold text-sm text-[#f5ebd7] tracking-wide">مجموعة إياس</div>
                    <div
                        class="text-[10px] font-semibold text-amber-500/80 uppercase tracking-widest font-latin mt-0.5">
                        EYAS Media Group</div>
                </a>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-2">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#c5a059] to-[#b38b40] text-white font-bold shadow-lg shadow-[#c5a059]/25' : 'text-slate-400 hover:text-[#f5ebd7] hover:bg-slate-800/60 font-medium' }} text-sm transition">
                        <i data-lucide="layout-grid" class="w-5 h-5 shrink-0"></i>
                        <span>الرئيسية</span>
                    </a>

                    @if (Route::has('team.index'))
                        <a href="{{ route('team.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('team.*') ? 'bg-gradient-to-r from-[#c5a059] to-[#b38b40] text-white font-bold shadow-lg shadow-[#c5a059]/25' : 'text-slate-400 hover:text-[#f5ebd7] hover:bg-slate-800/60 font-medium' }} text-sm transition">
                            <i data-lucide="users" class="w-5 h-5 shrink-0"></i>
                            <span>الفريق</span>
                        </a>
                    @endif

                    @if (Route::has('projects.index'))
                        <a href="{{ route('projects.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('projects.*') ? 'bg-gradient-to-r from-[#c5a059] to-[#b38b40] text-white font-bold shadow-lg shadow-[#c5a059]/25' : 'text-slate-400 hover:text-[#f5ebd7] hover:bg-slate-800/60 font-medium' }} text-sm transition">
                            <i data-lucide="folder-kanban" class="w-5 h-5 shrink-0"></i>
                            <span>المشاريع</span>
                        </a>
                    @endif

                    @if (Route::has('tasks.index'))
                        <a href="{{ route('tasks.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('tasks.*') ? 'bg-gradient-to-r from-[#c5a059] to-[#b38b40] text-white font-bold shadow-lg shadow-[#c5a059]/25' : 'text-slate-400 hover:text-[#f5ebd7] hover:bg-slate-800/60 font-medium' }} text-sm transition">
                            <i data-lucide="list-todo" class="w-5 h-5 shrink-0"></i>
                            <span>المهام</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="p-4 m-4 rounded-xl bg-slate-900/90 border border-slate-800">
                <div class="flex items-center gap-2 text-xs font-semibold text-amber-400">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>بيئة العمل</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">الإنتاج الإعلامي</div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header with Dynamic User Profile -->
            <header
                class="h-20 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-30 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                    <span>بوابة الإنتاج</span>
                    <i data-lucide="chevron-left" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-slate-800 font-bold">@yield('breadcrumb', 'المساحة التنفيذية')</span>
                </div>

                <div class="flex items-center gap-5">
                    <button @click="searchOpen = true"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-100 hover:border-amber-500/40 text-xs transition shadow-sm"
                        title="بحث سريع (Ctrl + K)">
                        <i data-lucide="search" class="w-4 h-4 text-[#c5a059]"></i>
                        <span class="text-xs font-medium">بحث سريع...</span>
                        <kbd
                            class="px-1.5 py-0.5 text-[10px] bg-white text-slate-500 rounded border border-slate-200 font-latin font-bold">Ctrl
                            K</kbd>
                    </button>

                    <button
                        class="relative text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-50 transition"
                        title="الإشعارات">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-amber-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Dynamic User Profile Menu -->
                    @php
                        $user = Auth::user();
                        $userName = $user?->name ?? 'صالح أبو إياس';
                        $userRole = $user?->role_name ?? 'المدير التنفيذي';
                        $userInitials = mb_substr($userName, 0, 2, 'UTF-8');
                    @endphp
                    <div class="flex items-center gap-3 pr-4 border-r border-slate-200" x-data="{ userMenuOpen: false }">
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-3 focus:outline-none">
                                <div class="text-left leading-tight hidden sm:block">
                                    <div class="text-sm font-bold text-slate-900">{{ $userName }}</div>
                                    <div class="text-[11px] text-[#9b7b32] font-semibold">{{ $userRole }}</div>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-full bg-[#fdf6e2] border border-[#d4af37]/60 flex items-center justify-center text-[#9b7b32] font-bold text-xs tracking-wider shadow-sm font-latin">
                                    {{ $userInitials }}
                                </div>
                            </button>

                            <!-- User Dropdown Menu -->
                            <div x-show="userMenuOpen" @click.outside="userMenuOpen = false" x-cloak
                                class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                                @if (Route::has('logout'))
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-right px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition">
                                            <i data-lucide="log-out" class="w-4 h-4"></i>
                                            <span>تسجيل الخروج</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-8 max-w-7xl w-full mx-auto space-y-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js Search Modal Component (Ctrl + K) -->
    <div x-show="searchOpen" x-cloak @keydown.escape.window="searchOpen = false"
        class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20 bg-slate-900/60 backdrop-blur-sm flex items-start justify-center">
        <div @click.outside="searchOpen = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="max-w-2xl w-full bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
            <!-- Search Bar -->
            <div class="relative flex items-center border-b border-slate-100 px-4 py-3 bg-slate-50/60">
                <i data-lucide="search" class="w-5 h-5 text-[#c5a059] shrink-0"></i>
                <input type="text" x-model="searchQuery"
                    placeholder="ابحث في المشاريع، أعضاء الفريق، أو المهام... (Ctrl + K)"
                    class="w-full bg-transparent pr-3 pl-8 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none font-medium"
                    x-ref="searchInput" x-init="$watch('searchOpen', value => { if (value) setTimeout(() => $refs.searchInput.focus(), 100); })">
                <button @click="searchOpen = false"
                    class="text-xs bg-white px-2 py-1 rounded border border-slate-200 text-slate-400 hover:text-slate-700 font-latin">ESC</button>
            </div>

            <!-- Dynamic Quick Navigation & Results -->
            <div class="p-4 max-h-96 overflow-y-auto space-y-4">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">روابط سريعة</div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#fdf6e2] hover:border-[#d4af37]/40 text-xs font-semibold text-slate-800 transition">
                            <i data-lucide="layout-grid" class="w-4 h-4 text-[#c5a059]"></i>
                            <span>الرئيسية</span>
                        </a>
                        @if (Route::has('team.index'))
                            <a href="{{ route('team.index') }}"
                                class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#fdf6e2] hover:border-[#d4af37]/40 text-xs font-semibold text-slate-800 transition">
                                <i data-lucide="users" class="w-4 h-4 text-[#c5a059]"></i>
                                <span>الفريق</span>
                            </a>
                        @endif
                        @if (Route::has('projects.index'))
                            <a href="{{ route('projects.index') }}"
                                class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#fdf6e2] hover:border-[#d4af37]/40 text-xs font-semibold text-slate-800 transition">
                                <i data-lucide="folder-kanban" class="w-4 h-4 text-[#c5a059]"></i>
                                <span>المشاريع</span>
                            </a>
                        @endif
                        @if (Route::has('tasks.index'))
                            <a href="{{ route('tasks.index') }}"
                                class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#fdf6e2] hover:border-[#d4af37]/40 text-xs font-semibold text-slate-800 transition">
                                <i data-lucide="list-todo" class="w-4 h-4 text-[#c5a059]"></i>
                                <span>المهام</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('modals')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>

</html>
