<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول المشاريع (projects)
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->comment('عنوان المشروع');
            $table->text('description')->nullable()->comment('وصف المشروع');

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete()
                ->comment('الدائرة التابع لها المشروع');

            $table->foreignId('owner_employee_id')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('الموظف المسؤول الرئيسي عن المشروع');

            $table->date('start_date')->comment('تاريخ البداية المخطط له');
            $table->date('end_date')->comment('تاريخ النهاية المخطط له (الموعد المستهدف)');
            $table->date('actual_end_date')->nullable()->comment('تاريخ الإغلاق الفعلي للمشروع');

            $table->enum('status', ['Draft', 'Active', 'On Hold', 'Completed', 'Cancelled'])
                ->default('Draft')
                ->comment('حالة المشروع');

            $table->decimal('completion_percentage', 5, 2)->default(0)->comment('نسبة الإنجاز المحسوبة');

            $table->timestamps();
            $table->softDeletes();

            // فهارس لتسريع حساب المشاريع المتأخرة والتقارير
            $table->index(['status', 'end_date']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
