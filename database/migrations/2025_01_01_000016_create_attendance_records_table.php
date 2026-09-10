<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول سجلات الحضور والانصراف (attendance_records)
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('الموظف صاحب السجل');

            $table->date('date')->comment('تاريخ يوم الحضور');
            $table->timestamp('check_in_time')->nullable()->comment('وقت الحضور الفعلي');
            $table->timestamp('check_out_time')->nullable()->comment('وقت الانصراف الفعلي');

            $table->enum('status', ['Present', 'Absent', 'Late', 'Leave'])
                ->comment('حالة الحضور اليومية');

            $table->unsignedSmallInteger('work_minutes')->nullable()->comment('إجمالي دقائق العمل المحسوبة لليوم');
            $table->unsignedSmallInteger('late_minutes')->default(0)->comment('دقائق التأخير إن وجدت');

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('من قام بتسجيل أو اعتماد هذا السجل');

            $table->timestamps();
            $table->softDeletes();

            // قيد تفرّد: سجل واحد فقط لكل موظف في اليوم الواحد
            $table->unique(['employee_id', 'date']);

            // فهارس للتقارير ولوحة حضور المدير التنفيذي
            $table->index(['date', 'status']);
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
