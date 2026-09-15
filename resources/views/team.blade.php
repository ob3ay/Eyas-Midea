@extends('layouts.app')
@section('title', 'فريق العمل والهيكلية الإدارية')
@section('breadcrumb', 'فريق شركة إياس')

@section('content')
<div x-data="teamDirectory()" x-init="init()" class="space-y-6">

    <!-- Top Banner & Work Hours Announcement -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0b1329] via-[#162345] to-[#0b1329] border border-slate-800 p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#c5a059]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#c5a059]/20 text-[#DFC288] border border-[#c5a059]/40">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        <span>الهيكل الإداري والتقني الموحد</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>22 كادر وموظف نشط</span>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#f5ebd7]">
                    فريق شركة إياس
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    منظومة الكوادر التنفيذية، الهندسية، الإعلامية والتسويقية لإدارة وتشغيل المشاريع التقنية والإنتاج الرقمي الموحد.
                </p>

                <!-- Official Working Hours Badge -->
                <div class="inline-flex flex-wrap items-center gap-2 p-2.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-xs">
                    <div class="w-7 h-7 rounded-xl bg-[#c5a059] text-slate-950 flex items-center justify-center font-bold">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="font-bold text-[#DFC288]">مواعيد الدوام الرسمي:</span>
                        <span class="text-slate-200 mr-1 font-medium">من الأحد إلى الخميس</span>
                        <span class="text-amber-300 font-latin font-bold mx-1" dir="ltr">(09:30 AM - 05:00 PM)</span>
                        <span class="text-slate-400 text-[11px]">(09:30 صباحاً حتى 05:00 مساءً)</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button @click="openAttendanceModal()" class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5">
                    <i data-lucide="fingerprint" class="w-4 h-4"></i>
                    <span>بصمة الدوام (Check-in/out)</span>
                </button>

                <button @click="openNewMemberModal()" class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-[#c5a059] to-[#b38b40] hover:from-[#b38b40] hover:to-[#9a7632] text-white text-xs font-bold shadow-lg shadow-[#c5a059]/25 transition transform hover:-translate-y-0.5">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>إضافة عضو جديد</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Team -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">إجمالي الكادر البشري</div>
                    <div class="text-3xl font-black text-slate-900 mt-1">22</div>
                    <div class="text-[11px] text-emerald-600 font-bold mt-1 flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                        <span>4 دوائر متكاملة</span>
                    </div>
                </div>
                <div class="w-13 h-13 p-3.5 rounded-2xl bg-[#fdf6e2] text-[#9b7b32] border border-[#c5a059]/30 flex items-center justify-center">
                    <i data-lucide="users-round" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Tech Team -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">فريق البرمجة والتقنية</div>
                    <div class="text-3xl font-black text-blue-600 mt-1">15</div>
                    <div class="text-[11px] text-blue-500 font-bold mt-1 flex items-center gap-1">
                        <i data-lucide="code" class="w-3 h-3"></i>
                        <span>مهندسون ومطورون</span>
                    </div>
                </div>
                <div class="w-13 h-13 p-3.5 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                    <i data-lucide="cpu" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Executive, Media & Marketing -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">التنفيذية والإعلام والتسويق</div>
                    <div class="text-3xl font-black text-amber-600 mt-1">7</div>
                    <div class="text-[11px] text-amber-600 font-bold mt-1 flex items-center gap-1">
                        <i data-lucide="briefcase" class="w-3 h-3"></i>
                        <span>قيادة وتنسيق وإعلام</span>
                    </div>
                </div>
                <div class="w-13 h-13 p-3.5 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                    <i data-lucide="crown" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Working Hours & Attendance -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-400 font-semibold">ساعات العمل اليومية</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1 font-latin">7.5h <span class="text-xs font-normal text-slate-400 font-tajawal">/ يوم</span></div>
                    <div class="text-[11px] text-emerald-600 font-bold mt-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>09:30 ص - 05:00 م</span>
                    </div>
                </div>
                <div class="w-13 h-13 p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                    <i data-lucide="calendar-clock" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search Controls -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Department Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200/70">
                <button 
                    @click="setDepartment('all')" 
                    :class="selectedDept === 'all' ? 'bg-white text-slate-900 font-bold shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-2"
                >
                    <span>الكل</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="selectedDept === 'all' ? 'bg-[#c5a059] text-white' : 'bg-slate-200 text-slate-700'" x-text="members.length">22</span>
                </button>

                <button 
                    @click="setDepartment('tech')" 
                    :class="selectedDept === 'tech' ? 'bg-white text-blue-700 font-bold shadow-sm border border-blue-200' : 'text-slate-600 hover:text-blue-700 font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-2"
                >
                    <i data-lucide="code-2" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>فريق البرمجة والتقنية</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="selectedDept === 'tech' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800'">15</span>
                </button>

                <button 
                    @click="setDepartment('executive')" 
                    :class="selectedDept === 'executive' ? 'bg-white text-amber-800 font-bold shadow-sm border border-amber-200' : 'text-slate-600 hover:text-amber-800 font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-2"
                >
                    <i data-lucide="crown" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>الدائرة التنفيذية</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="selectedDept === 'executive' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800'">4</span>
                </button>

                <button 
                    @click="setDepartment('media')" 
                    :class="selectedDept === 'media' ? 'bg-white text-purple-700 font-bold shadow-sm border border-purple-200' : 'text-slate-600 hover:text-purple-700 font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-2"
                >
                    <i data-lucide="video" class="w-3.5 h-3.5 text-purple-600"></i>
                    <span>دائرة الإعلام</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="selectedDept === 'media' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-800'">2</span>
                </button>

                <button 
                    @click="setDepartment('marketing')" 
                    :class="selectedDept === 'marketing' ? 'bg-white text-emerald-700 font-bold shadow-sm border border-emerald-200' : 'text-slate-600 hover:text-emerald-700 font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-2"
                >
                    <i data-lucide="megaphone" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>دائرة التسويق</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="selectedDept === 'marketing' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800'">1</span>
                </button>
            </div>

            <!-- Search and View Toggle -->
            <div class="flex items-center gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        placeholder="ابحث بالاسم، المسمى، أو التخصص..." 
                        class="w-full py-2.5 pr-10 pl-9 bg-slate-50 hover:bg-white focus:bg-white rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#c5a059] shadow-sm transition"
                    >
                    <div class="absolute right-3.5 top-3 text-slate-400 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <button 
                        x-show="searchQuery" 
                        @click="searchQuery = ''" 
                        class="absolute left-3 top-2.5 text-slate-400 hover:text-slate-600 p-0.5 rounded"
                    >
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- View Mode Switcher -->
                <div class="flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/80 shrink-0">
                    <button 
                        @click="viewMode = 'cards'" 
                        :class="viewMode === 'cards' ? 'bg-white text-[#9b7b32] font-bold shadow-sm' : 'text-slate-400 hover:text-slate-700'"
                        class="p-2 rounded-lg transition" 
                        title="عرض كبطاقات"
                    >
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </button>
                    <button 
                        @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white text-[#9b7b32] font-bold shadow-sm' : 'text-slate-400 hover:text-slate-700'"
                        class="p-2 rounded-lg transition" 
                        title="عرض كجدول"
                    >
                        <i data-lucide="table" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Filter Indicator -->
        <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
            <div class="flex items-center gap-2">
                <span>النتائج المعروضة:</span>
                <span class="font-bold text-slate-800" x-text="filteredMembers().length + ' من أصل ' + members.length + ' موظف'"></span>
                <span x-show="searchQuery" class="text-slate-400 font-medium" x-text="'(تصفية البحث: ' + searchQuery + ')'"></span>
            </div>
            <div class="text-[11px] text-slate-400">
                <span>دوام العمل: 09:30 ص - 05:00 م</span>
            </div>
        </div>
    </div>

    <!-- CARDS VIEW -->
    <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="member in filteredMembers()" :key="member.id">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group hover:border-[#c5a059]/40 relative overflow-hidden">
                
                <!-- Department Accent Stripe -->
                <div class="absolute top-0 right-0 left-0 h-1.5" :class="getDeptColor(member.department)"></div>

                <div>
                    <!-- Card Top Header -->
                    <div class="flex items-start justify-between gap-3 mb-4 pt-1">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-sm select-none" :class="member.avatarBg">
                                <span x-text="member.avatarIcon"></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-[#9b7b32] transition" x-text="member.name"></h3>
                                <div class="text-[11px] font-bold text-slate-500 font-latin" x-text="member.roleEn"></div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>نشط</span>
                        </span>
                    </div>

                    <!-- Role & Department Info -->
                    <div class="space-y-2.5 my-4 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/80 border border-slate-100">
                            <span class="text-slate-400 font-medium">المسمى الوظيفي:</span>
                            <span class="font-bold text-slate-800" x-text="member.roleAr"></span>
                        </div>

                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/80 border border-slate-100">
                            <span class="text-slate-400 font-medium">الدائرة / القسم:</span>
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-bold" :class="getDeptBadgeStyle(member.department)" x-text="member.deptName"></span>
                        </div>

                        <div class="flex items-start justify-between p-2.5 rounded-xl bg-slate-50/80 border border-slate-100">
                            <span class="text-slate-400 font-medium shrink-0">المهمة / المشروع:</span>
                            <span class="font-medium text-slate-700 text-left text-[11px]" x-text="member.project"></span>
                        </div>
                    </div>
                </div>

                <!-- Card Actions Footer -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-2">
                    <span class="text-[10px] text-slate-400 font-latin font-medium" x-text="member.email" dir="ltr"></span>
                    <div class="flex items-center gap-1.5">
                        <button @click="openMemberDetails(member)" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-[#fdf6e2] hover:text-[#9b7b32] text-slate-700 text-xs font-bold transition flex items-center gap-1">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>التفاصيل</span>
                        </button>
                        <button @click="quickMessage(member)" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="إرسال تنبيه">
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold">
                        <th class="py-4 px-6">الموظف</th>
                        <th class="py-4 px-6">الدائرة</th>
                        <th class="py-4 px-6">المسمى والتخصص التقني</th>
                        <th class="py-4 px-6">المهام والمشاريع</th>
                        <th class="py-4 px-6">الدوام والحالة</th>
                        <th class="py-4 px-6 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <template x-for="member in filteredMembers()" :key="member.id">
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-xs shadow-sm select-none" :class="member.avatarBg">
                                        <span x-text="member.avatarIcon"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-extrabold text-slate-900" x-text="member.name"></div>
                                        <div class="text-[11px] text-slate-400 font-latin" x-text="member.email" dir="ltr"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold" :class="getDeptBadgeStyle(member.department)" x-text="member.deptName"></span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900" x-text="member.roleAr"></div>
                                <div class="text-[11px] text-slate-400 font-latin" x-text="member.roleEn"></div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs border border-slate-200/60 font-medium">
                                    <i data-lucide="folder" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span x-text="member.project"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700 font-bold text-xs bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>حاضر (نشط)</span>
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-1 font-latin">09:30 AM - 05:00 PM</div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="openMemberDetails(member)" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-[#fdf6e2] hover:text-[#9b7b32] text-slate-700 text-xs font-bold transition">
                                        عرض
                                    </button>
                                    <button @click="quickMessage(member)" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="إرسال تنبيه">
                                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="filteredMembers().length === 0" class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-[#c5a059] flex items-center justify-center mx-auto mb-4 border border-amber-200">
            <i data-lucide="search-x" class="w-8 h-8"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800">لم يتم العثور على أي نتائج</h3>
        <p class="text-xs text-slate-500 mt-1">يرجى تعديل معايير البحث أو اختيار قسم آخر.</p>
        <button @click="selectedDept = 'all'; searchQuery = ''" class="mt-4 px-4 py-2 rounded-xl bg-[#c5a059] text-white text-xs font-bold transition hover:bg-[#b38b40]">
            إعادة تعيين الفلاتر
        </button>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- 1. ATTENDANCE (CHECK-IN / CHECK-OUT) MODAL -->
    <div x-show="attendanceModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="attendanceModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200">
            
            <button @click="attendanceModalOpen = false" class="absolute top-5 left-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 border border-emerald-200 shadow-sm">
                    <i data-lucide="fingerprint" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900">تسجيل بصمة الدوام الذكية</h3>
                <p class="text-xs text-slate-500 mt-1">شركة إياس (Eyas)</p>
            </div>

            <!-- Official Hours Highlight -->
            <div class="mb-5 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs space-y-1">
                <div class="flex items-center justify-between text-[#9b7b32] font-bold">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>الدوام الرسمي المعتمد:</span>
                    </span>
                    <span class="bg-[#c5a059] text-white px-2 py-0.5 rounded-lg text-[11px] font-latin">09:30 AM - 05:00 PM</span>
                </div>
                <p class="text-slate-600 text-[11px]">من يوم الأحد حتى الخميس (09:30 صباحاً حتى 05:00 مساءً) — 7.5 ساعات عمل يومياً.</p>
            </div>

            <!-- Live Digital Clock Display -->
            <div class="bg-slate-900 text-white rounded-2xl p-5 text-center mb-5 shadow-inner border border-slate-800">
                <div class="text-[11px] font-bold text-[#c5a059] uppercase tracking-wider mb-1">الوقت المعتمد المباشر</div>
                <div class="text-3xl sm:text-4xl font-black font-latin text-amber-400 clock-display" x-text="currentTime">09:30:00 AM</div>
                <div class="text-xs text-slate-400 mt-1.5" x-text="currentDate"></div>
                
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-around text-xs">
                    <div>
                        <div class="text-slate-400 text-[10px]">بداية الدوام</div>
                        <div class="font-bold text-white font-latin mt-0.5">09:30 AM</div>
                    </div>
                    <div class="w-px h-6 bg-slate-700"></div>
                    <div>
                        <div class="text-slate-400 text-[10px]">نهاية الدوام</div>
                        <div class="font-bold text-white font-latin mt-0.5">05:00 PM</div>
                    </div>
                    <div class="w-px h-6 bg-slate-700"></div>
                    <div>
                        <div class="text-slate-400 text-[10px]">حالة البصمة</div>
                        <div class="font-bold font-latin mt-0.5" :class="isCheckedIn ? 'text-emerald-400' : 'text-slate-400'" x-text="isCheckedIn ? 'Checked In' : 'Checked Out'"></div>
                    </div>
                </div>
            </div>

            <!-- Check In / Out Toggle Action -->
            <div class="space-y-3">
                <button 
                    @click="toggleAttendanceState()" 
                    :class="isCheckedIn ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-900/20'"
                    class="w-full py-3.5 px-4 rounded-2xl border font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2"
                >
                    <i :data-lucide="isCheckedIn ? 'log-out' : 'log-in'" class="w-4 h-4"></i>
                    <span x-text="isCheckedIn ? 'تسجيل الانصراف (Check Out)' : 'تسجيل الحضور (Check In)'"></span>
                </button>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>حالة النظام: متصل بالخادم السحابي</span>
                    <span class="text-emerald-600 font-bold">بصمة موثقة 100%</span>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. MEMBER DETAILS MODAL -->
    <div x-show="memberDetailsModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="memberDetailsModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200">
            
            <button @click="memberDetailsModalOpen = false" class="absolute top-5 left-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <template x-if="selectedMember">
                <div>
                    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-bold text-xl shadow-sm" :class="selectedMember.avatarBg">
                            <span x-text="selectedMember.avatarIcon"></span>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900" x-text="selectedMember.name"></h3>
                            <div class="text-xs font-bold text-[#9b7b32] font-latin" x-text="selectedMember.roleEn"></div>
                            <div class="text-xs text-slate-500 mt-0.5" x-text="selectedMember.roleAr"></div>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs mb-6">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <span class="text-slate-400 font-medium">الدائرة:</span>
                            <span class="font-bold text-slate-800" x-text="selectedMember.deptName"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <span class="text-slate-400 font-medium">البريد الإلكتروني:</span>
                            <span class="font-bold font-latin text-slate-800" x-text="selectedMember.email" dir="ltr"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <span class="text-slate-400 font-medium">مواعيد الدوام:</span>
                            <span class="font-bold text-slate-800">الأحد - الخميس (09:30 ص - 05:00 م)</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start justify-between">
                            <span class="text-slate-400 font-medium shrink-0">المشروع الحالي:</span>
                            <span class="font-bold text-slate-800 text-left" x-text="selectedMember.project"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button @click="quickMessage(selectedMember); memberDetailsModalOpen = false;" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#c5a059] to-[#b38b40] text-white font-bold text-xs transition">
                            إرسال رسالة مباشرة
                        </button>
                        <button @click="memberDetailsModalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition">
                            إغلاق
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

    <!-- 3. ADD NEW MEMBER MODAL -->
    <div x-show="newMemberModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="newMemberModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200">
            
            <button @click="newMemberModalOpen = false" class="absolute top-5 left-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-[#c5a059] flex items-center justify-center mx-auto mb-3 border border-amber-200 shadow-sm">
                    <i data-lucide="user-plus" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900">إضافة عضو جديد للفريق</h3>
                <p class="text-xs text-slate-500 mt-1">تنسيب موظف جديد في شركة إياس</p>
            </div>

            <form @submit.prevent="addNewMember()" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-1.5">الاسم الكامل:</label>
                    <input type="text" x-model="newMemberData.name" required placeholder="مثال: م. أحمد السعيد" class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#c5a059] text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">الدائرة:</label>
                        <select x-model="newMemberData.department" class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#c5a059] text-xs bg-white">
                            <option value="tech">فريق البرمجة والتقنية</option>
                            <option value="executive">الدائرة التنفيذية</option>
                            <option value="media">دائرة الإعلام</option>
                            <option value="marketing">دائرة التسويق</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">المسمى بالعربية:</label>
                        <input type="text" x-model="newMemberData.roleAr" required placeholder="مثال: مطور برمجيات" class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#c5a059] text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">المسمى بالإنجليزية:</label>
                        <input type="text" x-model="newMemberData.roleEn" required placeholder="مثال: Software Engineer" class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#c5a059] text-xs font-latin" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-slate-700 font-bold mb-1.5">المشروع المكلف به:</label>
                        <input type="text" x-model="newMemberData.project" placeholder="مثال: منصة التجارة الرقمية" class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#c5a059] text-xs">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#c5a059] to-[#b38b40] text-white font-bold text-xs shadow-md shadow-[#c5a059]/30 hover:from-[#b38b40] hover:to-[#9a7632] transition">
                        حفظ وإضافة العضو
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

@push('scripts')
<script>
function teamDirectory() {
    return {
        selectedDept: 'all',
        searchQuery: '',
        viewMode: 'cards',
        attendanceModalOpen: false,
        memberDetailsModalOpen: false,
        newMemberModalOpen: false,
        selectedMember: null,
        isCheckedIn: true,
        currentTime: '09:30:00 AM',
        currentDate: 'الأحد - الخميس (09:30 ص - 05:00 م)',
        
        newMemberData: {
            name: '',
            department: 'tech',
            roleAr: '',
            roleEn: '',
            project: ''
        },

        // Complete Team & Hierarchy Data (22 members)
        members: [
            // ================= 1. EXECUTIVE DEPARTMENT (4 members) =================
            {
                id: 1,
                name: 'صالح رزق محمد حسنين',
                roleAr: 'رئيس مجلس الإدارة',
                roleEn: 'Chairman of the Board',
                department: 'executive',
                deptName: 'الدائرة التنفيذية',
                email: 'saleh.hasaneen@eyas.ps',
                project: 'رئاسة مجلس الإدارة والتوجيه الاستراتيجي العام',
                avatarBg: 'bg-amber-100 text-amber-900 border border-amber-300',
                avatarIcon: '👑'
            },
            {
                id: 2,
                name: 'محمد أبو شحمة',
                roleAr: 'المدير التنفيذي',
                roleEn: 'Chief Executive Officer (CEO)',
                department: 'executive',
                deptName: 'الدائرة التنفيذية',
                email: 'm.abushahma@eyas.ps',
                project: 'إدارة العمليات والتنسيق العام',
                avatarBg: 'bg-slate-800 text-amber-400 border border-slate-700',
                avatarIcon: '🏛️'
            },
            {
                id: 3,
                name: 'ولاء أبو دقة',
                roleAr: 'نائب المدير',
                roleEn: 'Deputy Director',
                department: 'executive',
                deptName: 'الدائرة التنفيذية',
                email: 'w.abudaqqa@eyas.ps',
                project: 'المتابعة الإدارية والتطوير التنفيذي',
                avatarBg: 'bg-amber-800 text-white border border-amber-700',
                avatarIcon: '⭐'
            },
            {
                id: 4,
                name: 'البراء عمر',
                roleAr: 'الشؤون القانونية',
                roleEn: 'Legal Affairs Officer',
                department: 'executive',
                deptName: 'الدائرة التنفيذية',
                email: 'b.omar@eyas.ps',
                project: 'الاستشارات والعقود القانونية والامتثال',
                avatarBg: 'bg-slate-700 text-white border border-slate-600',
                avatarIcon: '⚖️'
            },

            // ================= 2. EYAS TECH & PROGRAMMING (15 members) =================
            {
                id: 5,
                name: 'إسلام',
                roleAr: 'قائد فريق البرمجة (Tech Lead)',
                roleEn: 'Tech Lead & Architect',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'eslam@eyas.ps',
                project: 'هندسة الأنظمة وإدارة الفريق التقني والمشاريع',
                avatarBg: 'bg-slate-900 text-amber-400 border border-slate-800',
                avatarIcon: '⚡'
            },
            {
                id: 6,
                name: 'نبال',
                roleAr: 'مهندسة باك إند (Senior Back-End)',
                roleEn: 'Senior Back-End Developer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'nebal@eyas.ps',
                project: 'بناء المعمارية الخلفية والأنظمة المركزية',
                avatarBg: 'bg-indigo-900 text-indigo-200 border border-indigo-700',
                avatarIcon: '💻'
            },
            {
                id: 7,
                name: 'محمود',
                roleAr: 'مطور لارافيل (Laravel Developer)',
                roleEn: 'Laravel Developer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'mahmoud@eyas.ps',
                project: 'تطوير وتكامل منصات Laravel و APIs',
                avatarBg: 'bg-rose-900 text-rose-200 border border-rose-700',
                avatarIcon: '🔥'
            },
            {
                id: 8,
                name: 'لؤي',
                roleAr: 'مطور واجهات (Front-End Specialist)',
                roleEn: 'Front-End Specialist',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'loai@eyas.ps',
                project: 'بناء وتطوير واجهات المستخدم التفاعلية السريعة',
                avatarBg: 'bg-cyan-900 text-cyan-200 border border-cyan-700',
                avatarIcon: '🎨'
            },
            {
                id: 9,
                name: 'أُبي',
                roleAr: 'مهندس قواعد بيانات (Database Architect)',
                roleEn: 'Database Architect',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'obai@eyas.ps',
                project: 'تصميم وتحسين أداء ومخططات قواعد البيانات',
                avatarBg: 'bg-emerald-900 text-emerald-200 border border-emerald-700',
                avatarIcon: '🗄️'
            },
            {
                id: 10,
                name: 'أنس',
                roleAr: 'مبرمج تطبيقات (Mobile App Developer)',
                roleEn: 'Mobile App Developer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'anas@eyas.ps',
                project: 'تطوير تطبيقات الهواتف الذكية (iOS & Android)',
                avatarBg: 'bg-blue-900 text-blue-200 border border-blue-700',
                avatarIcon: '📱'
            },
            {
                id: 11,
                name: 'يوسف',
                roleAr: 'مهندس جودة ودعم (QA Engineer)',
                roleEn: 'QA & Support Engineer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'yousef@eyas.ps',
                project: 'فحص الجودة واختبار الأداء والدعم الفني',
                avatarBg: 'bg-teal-900 text-teal-200 border border-teal-700',
                avatarIcon: '🛡️'
            },
            {
                id: 12,
                name: 'سارة',
                roleAr: 'مصممة واجهات (UI/UX Designer)',
                roleEn: 'UI/UX Designer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'sara@eyas.ps',
                project: 'تصميم تجارب المستخدم وواجهات المنتجات الرقمية',
                avatarBg: 'bg-pink-900 text-pink-200 border border-pink-700',
                avatarIcon: '✨'
            },
            {
                id: 13,
                name: 'حمزة',
                roleAr: 'مهندس أمن معلومات (Cybersecurity Specialist)',
                roleEn: 'Cybersecurity Specialist',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'hamza@eyas.ps',
                project: 'حماية وتأمين البنية الرقمية واختبار الاختراق',
                avatarBg: 'bg-red-950 text-red-300 border border-red-800',
                avatarIcon: '🔒'
            },
            {
                id: 14,
                name: 'عمر',
                roleAr: 'مهندس سيرفرات (DevOps Engineer)',
                roleEn: 'DevOps & Cloud Engineer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'omar@eyas.ps',
                project: 'إدارة الخوادم، السحابة وخطوط النشر المستمر CI/CD',
                avatarBg: 'bg-slate-800 text-emerald-400 border border-slate-700',
                avatarIcon: '☁️'
            },
            {
                id: 15,
                name: 'رنيم',
                roleAr: 'مطورة full-stack (Full-Stack Developer)',
                roleEn: 'Full-Stack Developer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'raneem@eyas.ps',
                project: 'تطوير المنظومات المتكاملة من الواجهة إلى الخادم',
                avatarBg: 'bg-purple-900 text-purple-200 border border-purple-700',
                avatarIcon: '🚀'
            },
            {
                id: 16,
                name: 'كريم',
                roleAr: 'مبرمج API (API Integrator)',
                roleEn: 'API Integrator',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'kareem@eyas.ps',
                project: 'ربط وتكامل بوابات الدفع والخدمات السحابية',
                avatarBg: 'bg-amber-950 text-amber-300 border border-amber-800',
                avatarIcon: '🔗'
            },
            {
                id: 17,
                name: 'شهد',
                roleAr: 'تحليل نظم (System Analyst)',
                roleEn: 'System Analyst',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'shahd@eyas.ps',
                project: 'هندسة المتطلبات والمخططات الفنية للأنظمة',
                avatarBg: 'bg-sky-900 text-sky-200 border border-sky-700',
                avatarIcon: '📊'
            },
            {
                id: 18,
                name: 'طارق',
                roleAr: 'مطور حلول ذكية (Software Developer)',
                roleEn: 'Software & Smart Solutions Developer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'tareq@eyas.ps',
                project: 'برمجة الحلول والأدوات الذكية المؤتمتة',
                avatarBg: 'bg-violet-900 text-violet-200 border border-violet-700',
                avatarIcon: '💡'
            },
            {
                id: 19,
                name: 'أحمد',
                roleAr: 'مبرمج وبحوث تقنية (R&D Developer)',
                roleEn: 'R&D Developer',
                department: 'tech',
                deptName: 'فريق البرمجة والتقنية',
                email: 'ahmed.rd@eyas.ps',
                project: 'البحث التقني والتطوير واستكشاف التقنيات الحديثة',
                avatarBg: 'bg-emerald-950 text-emerald-300 border border-emerald-800',
                avatarIcon: '🔬'
            },

            // ================= 3. MEDIA DEPARTMENT (2 members) =================
            {
                id: 20,
                name: 'براء أبو عامر',
                roleAr: 'منسق دائرة الإعلام',
                roleEn: 'Media Department Coordinator',
                department: 'media',
                deptName: 'دائرة الإعلام',
                email: 'baraa.amer@eyas.ps',
                project: 'تنسيق الإنتاج المرئي والمحتوى الإعلامي',
                avatarBg: 'bg-purple-900 text-purple-200 border border-purple-700',
                avatarIcon: '🎬'
            },
            {
                id: 21,
                name: 'لؤي بركة',
                roleAr: 'قسم الإعلام الرقمي',
                roleEn: 'Digital Media Specialist',
                department: 'media',
                deptName: 'دائرة الإعلام',
                email: 'loai.baraka@eyas.ps',
                project: 'إدارة وتوجيه منصات النشر الرقمي وصناعة المحتوى',
                avatarBg: 'bg-violet-900 text-violet-200 border border-violet-700',
                avatarIcon: '📡'
            },

            // ================= 4. MARKETING DEPARTMENT (1 member) =================
            {
                id: 22,
                name: 'محمد الغلبان',
                roleAr: 'ائتلاف الإغاثة الإنسانية / التسويق',
                roleEn: 'Marketing & Relief Coalition Specialist',
                department: 'marketing',
                deptName: 'دائرة التسويق',
                email: 'm.ghalban@eyas.ps',
                project: 'إدارة حملات التسويق وشراكات ائتلاف الإغاثة الإنسانية',
                avatarBg: 'bg-emerald-900 text-emerald-200 border border-emerald-700',
                avatarIcon: '🤝'
            }
        ],

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const period = now.getHours() < 12 ? 'AM' : 'PM';
            this.currentTime = `${hours}:${minutes}:${seconds} ${period}`;
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            this.currentDate = now.toLocaleDateString('ar-EG', options);
        },

        setDepartment(dept) {
            this.selectedDept = dept;
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        filteredMembers() {
            return this.members.filter(m => {
                const matchesDept = (this.selectedDept === 'all' || m.department === this.selectedDept);
                const query = this.searchQuery.trim().toLowerCase();
                const matchesSearch = !query || 
                    m.name.toLowerCase().includes(query) ||
                    m.roleAr.toLowerCase().includes(query) ||
                    m.roleEn.toLowerCase().includes(query) ||
                    m.project.toLowerCase().includes(query) ||
                    m.deptName.toLowerCase().includes(query) ||
                    m.email.toLowerCase().includes(query);
                
                return matchesDept && matchesSearch;
            });
        },

        getDeptColor(dept) {
            switch(dept) {
                case 'tech': return 'bg-blue-500';
                case 'executive': return 'bg-amber-500';
                case 'media': return 'bg-purple-500';
                case 'marketing': return 'bg-emerald-500';
                default: return 'bg-[#c5a059]';
            }
        },

        getDeptBadgeStyle(dept) {
            switch(dept) {
                case 'tech': return 'bg-blue-50 text-blue-800 border border-blue-200';
                case 'executive': return 'bg-amber-50 text-amber-900 border border-amber-200 font-bold';
                case 'media': return 'bg-purple-50 text-purple-800 border border-purple-200';
                case 'marketing': return 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                default: return 'bg-slate-100 text-slate-800 border border-slate-200';
            }
        },

        openAttendanceModal() {
            this.attendanceModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        toggleAttendanceState() {
            this.isCheckedIn = !this.isCheckedIn;
            if (this.isCheckedIn) {
                if (typeof showToast === 'function') {
                    showToast('تم تسجيل حضورك بنجاح في نظام الدوام الرسمي (09:30 ص - 05:00 م) ✨', 'success');
                }
            } else {
                if (typeof showToast === 'function') {
                    showToast('تم تسجيل الانصراف وحفظ ساعات العمل اليومية بنجاح.', 'info');
                }
            }
        },

        openMemberDetails(member) {
            this.selectedMember = member;
            this.memberDetailsModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        openNewMemberModal() {
            this.newMemberModalOpen = true;
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        addNewMember() {
            const newId = this.members.length + 1;
            const deptNames = {
                tech: 'فريق البرمجة والتقنية',
                executive: 'الدائرة التنفيذية',
                media: 'دائرة الإعلام',
                marketing: 'دائرة التسويق'
            };

            this.members.push({
                id: newId,
                name: this.newMemberData.name,
                roleAr: this.newMemberData.roleAr,
                roleEn: this.newMemberData.roleEn,
                department: this.newMemberData.department,
                deptName: deptNames[this.newMemberData.department] || 'عام',
                email: 'member' + newId + '@eyas.ps',
                project: this.newMemberData.project || 'المشاريع العامة',
                avatarBg: 'bg-slate-800 text-white',
                avatarIcon: '👤'
            });

            this.newMemberModalOpen = false;
            this.newMemberData = { name: '', department: 'tech', roleAr: '', roleEn: '', project: '' };

            if (typeof showToast === 'function') {
                showToast('تمت إضافة العضو الجديد بنجاح إلى الفريق!', 'success');
            }
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        quickMessage(member) {
            if (typeof showToast === 'function') {
                showToast(`تم فتح قناة تواصل سريعة مع: ${member.name}`, 'info');
            }
        }
    }
}
</script>
@endpush
@endsection

