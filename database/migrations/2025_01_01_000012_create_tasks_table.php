<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول المهام (tasks)
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->comment('عنوان المهمة');
            $table->text('description')->nullable()->comment('وصف تفصيلي للمهمة ومخرجاتها');

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete()
                ->comment('المشروع المرتبط بها (اختياري)');

            $table->foreignId('parent_task_id')
                ->nullable()
                ->constrained('tasks')
                ->cascadeOnDelete()
                ->comment('المهمة الرئيسية التابعة لها (للمهام الفرعية)');

            $table->foreignId('assignee_employee_id')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('الموظف المسندة إليه المهمة');

            $table->foreignId('created_by')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('منشئ المهمة');

            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])
                ->default('Medium')
                ->comment('أولوية التنفيذ');

            $table->dateTime('due_datetime')->nullable()->comment('الموعد النهائي للتسليم باليوم والساعة');

            $table->enum('status', ['Draft', 'To-Do', 'In Progress', 'Under Review', 'Completed', 'Rejected'])
                ->default('Draft')
                ->comment('حالة المهمة الحالية');

            $table->decimal('completion_percentage', 5, 2)->default(0)->comment('نسبة إنجاز المهمة');
            $table->timestamp('completed_at')->nullable()->comment('تاريخ ووقت الاكتمال الفعلي');

            $table->timestamps();
            $table->softDeletes();

            // فهارس استعلامية لتسريع لوحات كانبان وشاشات المتابعة والتقارير
            $table->index(['assignee_employee_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['status', 'due_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
