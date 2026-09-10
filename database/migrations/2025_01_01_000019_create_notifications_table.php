<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الإشعارات (notifications)
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recipient_user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('المستخدم مستلم الإشعار');

            $table->string('type', 50)->comment('نوع الحدث (task_assigned, task_overdue...)');
            $table->string('title', 200)->comment('عنوان الإشعار');
            $table->text('message')->comment('نص وتفاصيل الإشعار');
            $table->timestamp('read_at')->nullable()->comment('تاريخ ووقت القراءة (NULL تعني غير مقروء)');
            $table->boolen('isread')->default(false);
            // ربط متعدد بالكيان المرتبط (مهمة، مشروع، طلب حضور...)
            $table->nullableMorphs('related_entity');

            $table->timestamp('created_at')->useCurrent();

            $table->index(['recipient_user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
