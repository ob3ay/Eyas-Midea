<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول مرفقات المشاريع (project_attachments)
     */
    public function up(): void
    {
        Schema::create('project_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('file_name', 255)->comment('اسم الملف الأصلي');
            $table->string('file_path', 500)->comment('مسار تخزين الملف في storage');
            $table->unsignedInteger('file_size_kb')->comment('حجم الملف بالكيلوبايت');
            $table->string('mime_type', 100)->nullable()->comment('نوع الملف');
            $table->foreignId('uploaded_by')->nullable()->constrained('employees')->nullOnDelete()->comment('الموظف الذي رفع المرفق');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_attachments');
    }
};
