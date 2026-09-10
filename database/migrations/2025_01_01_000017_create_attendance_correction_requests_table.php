<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول طلبات تصحيح الحضور (attendance_correction_requests)
     */
    public function up(): void
    {
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_record_id')
                ->constrained('attendance_records')
                ->restrictOnDelete()
                ->comment('سجل الحضور المطلوب تصحيحه');

            $table->text('reason')->comment('مبرر أو سبب طلب التصحيح من الموظف');

            $table->enum('requested_status', ['Present', 'Absent', 'Late', 'Leave'])
                ->comment('الحالة المطلوبة');

            $table->timestamp('requested_check_in_time')->nullable()->comment('وقت الحضور المقترح تصحيحه');
            $table->timestamp('requested_check_out_time')->nullable()->comment('وقت الانصراف المقترح تصحيحه');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('حالة الطلب (FR-07)');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete()
                ->comment('المسؤول المراجع (المدير التنفيذي)');

            $table->timestamp('reviewed_at')->nullable()->comment('تاريخ ووقت البت في الطلب');
            $table->text('admin_notes')->nullable()->comment('ملاحظات المسؤول أو سبب رفض الطلب');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_requests');
    }
};
