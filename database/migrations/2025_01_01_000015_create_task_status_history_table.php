<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول سجل تغييرات حالة المهمة (task_status_history)
     */
    public function up(): void
    {
        Schema::create('task_status_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->enum('old_status', ['Draft', 'To-Do', 'In Progress', 'Under Review', 'Completed', 'Rejected'])
                ->comment('الحالة السابقة');
            $table->enum('new_status', ['Draft', 'To-Do', 'In Progress', 'Under Review', 'Completed', 'Rejected'])
                ->comment('الحالة الجديدة');

            $table->text('notes')->nullable()->comment('سبب تغيير الحالة أو ملاحظات الرفض/المراجعة');

            $table->foreignId('changed_by')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('من قام بتغيير الحالة');

            $table->timestamp('changed_at')->useCurrent()->comment('توقيت التغيير الدقيق');

            $table->index('task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_status_history');
    }
};
