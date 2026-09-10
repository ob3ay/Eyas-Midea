<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 30)->nullable()->unique()->comment('الرقم/الكود الوظيفي مثل EMP-001');
            $table->string('full_name', 200)->comment('الاسم الكامل للموظف');
            $table->string('national_id', 20)->nullable()->unique()->comment('رقم الهوية الوطنية أو الإقامة');

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete()
                ->comment('الدائرة التابع لها إلزاميًا');

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections')
                ->nullOnDelete()
                ->comment('القسم التابع له (اختياري)');

            $table->foreignId('manager_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete()
                ->comment('المدير المباشر (علاقة ذاتية)');

            $table->string('position', 100)->nullable()->comment('المسمى الوظيفي');
            $table->string('phone', 25)->nullable()->comment('رقم هاتف الموظف للتواصل');
            $table->string('avatar', 255)->nullable()->comment('مسار الصورة الشخصية');

            $table->enum('employment_status', ['active', 'inactive', 'on_leave'])
                ->default('active')
                ->comment('الحالة الوظيفية الحالية');

            $table->date('hire_date')->nullable()->comment('تاريخ التعيين');
            $table->date('contract_end_date')->nullable()->comment('تاريخ انتهاء العقد');

            $table->timestamps();
            $table->softDeletes();

            // فهارس إضافية لسرعة البحث والفرز
            $table->index(['department_id', 'employment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
