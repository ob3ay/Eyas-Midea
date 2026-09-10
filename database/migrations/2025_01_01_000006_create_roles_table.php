<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الأدوار (roles)
     * يدعم الأدوار السبعة الأساسية (SA, EX, DP, DM, SH, EMP, TA)
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique()->comment('رمز أو اسم الدور التقني (مثال: Super Admin, SA)');
            $table->string('display_name', 100)->nullable()->comment('الاسم الظاهر بالعربية (مثال: رئيس مجلس الإدارة)');
            $table->text('description')->nullable()->comment('وصف مختصر لمهام ومسؤوليات الدور');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
