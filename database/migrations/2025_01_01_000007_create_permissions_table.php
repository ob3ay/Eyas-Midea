<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الصلاحيات (permissions)
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('مفتاح الصلاحية البرمجي (مثال: task.create_assign)');
            $table->string('display_name', 150)->nullable()->comment('الاسم الظاهر للصلاحية بالعربية');
            $table->text('description')->nullable()->comment('وصف تفصيلي لما تخوله الصلاحية');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
