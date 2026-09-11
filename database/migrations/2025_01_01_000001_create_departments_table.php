<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الدوائر (departments)
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable()->unique()->comment('كود الدائرة المختصر مثل PRD, HR');
            $table->string('name', 150)->unique()->comment('اسم الدائرة');
            $table->unsignedBigInteger('manager_employee_id')->nullable()->comment('مدير الدائرة');
            $table->string('description', 500)->nullable()->comment('وصف الدائرة');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
