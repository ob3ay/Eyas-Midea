<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول تقييمات الأداء (performance_evaluations)
     */
    public function up(): void
    {
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('الموظف المقيَّم');

            $table->foreignId('evaluator_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('المستخدم المقيم');

            $table->date('evaluation_date')->comment('تاريخ إجراء التقييم');
            $table->string('period', 30)->nullable()->comment('دورة التقييم مثل 2025-Q1 أو 2025-Annual');

            $table->decimal('score', 5, 2)->nullable()->comment('الدرجة الإجمالية (من 0.00 إلى 100.00)');
            $table->text('notes')->nullable()->comment('الملاحظات النوعية وتوصيات التطوير');

            $table->boolean('is_finalized')->default(false)->comment('هل تم الاعتماد النهائي (غير قابل للتعديل بعدها FR-09)');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_evaluations');
    }
};
