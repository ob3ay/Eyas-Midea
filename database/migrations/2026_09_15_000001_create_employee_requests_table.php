<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول طلبات الموظفين والخدمة الذاتية (employee_requests)
     */
    public function up(): void
    {
        Schema::create('employee_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->comment('الموظف صاحب الطلب');

            $table->enum('type', ['leave', 'advance', 'certificate', 'other'])
                ->comment('نوع الطلب: إجازة، سلفة، تعريف راتب، أخرى');

            $table->string('title')->comment('عنوان الطلب أو ملخصه');
            $table->text('reason')->nullable()->comment('مبرر وتفاصيل الطلب');

            $table->date('start_date')->nullable()->comment('تاريخ بدء الإجازة');
            $table->date('end_date')->nullable()->comment('تاريخ انتهاء الإجازة');
            $table->unsignedInteger('days_count')->nullable()->comment('عدد أيام الإجازة');

            $table->decimal('amount', 10, 2)->nullable()->comment('المبلغ المالي للسلف أو العهد');

            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('pending')
                ->comment('حالة الطلب: قيد المراجعة، معتمد، مرفوض، ملغي');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('المستخدم الإداري المراجع');

            $table->timestamp('reviewed_at')->nullable()->comment('تاريخ ووقت البت في الطلب');
            $table->text('admin_notes')->nullable()->comment('ملاحظات الإدارة أو سبب الرفض');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_requests');
    }
};
