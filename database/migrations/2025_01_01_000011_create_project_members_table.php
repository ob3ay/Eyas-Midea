<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول أعضاء فريق المشروع (project_members)
     */
    public function up(): void
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('role_in_project', 60)->nullable()->comment('دور الموظف داخل المشروع (مثال: مخرج، مصمم، كاتب محتوى)');
            $table->timestamp('joined_at')->useCurrent()->comment('تاريخ انضمام الموظف للمشروع');

            $table->primary(['project_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
