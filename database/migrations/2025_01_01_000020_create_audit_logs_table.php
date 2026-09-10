<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول سجل التدقيق الأمني (audit_logs)
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('المستخدم المنفذ للعملية (أو NULL للعمليات المؤتمتة)');

            $table->string('action_type', 50)->comment('نوع العملية (Create, Update, Delete, Approve, Reject...)');
            $table->string('entity_type', 50)->comment('نوع الكيان المتأثر (Task, Project, Attendance...)');
            $table->unsignedBigInteger('entity_id')->comment('معرف الكيان المتأثر');

            $table->json('old_value')->nullable()->comment('القيم قبل التعديل');
            $table->json('new_value')->nullable()->comment('القيم بعد التعديل');

            $table->string('ip_address', 45)->nullable()->comment('عنوان IP للطلب');
            $table->text('user_agent')->nullable()->comment('معلومات المتصفح ونظام التشغيل (NFR-08)');

            $table->timestamp('created_at')->useCurrent()->comment('وقت حدوث العملية');

            // فهارس للبحث والتدقيق الجنائي السريع
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
