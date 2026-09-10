<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->unique()
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('الموظف صاحب الحساب (علاقة 1:1)');

            $table->string('username', 60)->unique()->comment('اسم المستخدم لتسجيل الدخول');
            $table->string('email')->unique()->comment('البريد الإلكتروني للعمل واستعادة كلمة المرور');
            $table->string('password')->comment('كلمة المرور المشفرة Hash (NFR-01)');
            $table->boolean('is_active')->default(true)->comment('هل الحساب مفعّل');
            $table->unsignedSmallInteger('failed_login_attempts')->default(0)->comment('محاولات الدخول الفاشلة (FR-02)');
            $table->timestamp('locked_until')->nullable()->comment('وقت فك قفل الحساب المؤقت');
            $table->timestamp('last_login_at')->nullable()->comment('آخر تسجيل دخول ناجح');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
