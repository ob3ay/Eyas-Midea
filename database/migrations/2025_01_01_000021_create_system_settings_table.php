<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الإعدادات التقنية العامة (system_settings)
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('مفتاح الإعداد (مثال: backup_retention_days)');
            $table->text('value')->comment('قيمة الإعداد');
            $table->string('description', 255)->nullable()->comment('وصف وظيفة الإعداد');

            $table->foreignId('updated_by')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('آخر مستخدم قام بتعديل هذا الإعداد');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
