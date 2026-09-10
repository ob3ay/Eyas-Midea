<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * جدول مرفقات المهام (task_attachments)
     */
    public function up(): void
    {
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->string('file_name', 255)->comment('اسم الملف الأصلي');
            $table->string('file_path', 500)->comment('مسار تخزين الملف في السيرفر');
            $table->unsignedInteger('file_size_kb')->comment('حجم الملف بالكيلوبايت');
            $table->string('mime_type', 100)->comment('نوع MIME للملف');

            $table->foreignId('uploaded_by')
                ->constrained('employees')
                ->restrictOnDelete()
                ->comment('من رفع المرفق');

            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
        });

        // قيد أمان على مستوى محرك قاعدة البيانات: أقصى حجم 10 ميغابايت (10240 KB)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE task_attachments ADD CONSTRAINT chk_task_attachments_size CHECK (file_size_kb <= 10240)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};
