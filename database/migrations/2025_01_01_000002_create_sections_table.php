<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('اسم القسم');
            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete()  //casecateon delete
                ->comment('الدائرة التابع لها القسم');
            $table->unsignedBigInteger('section_head_employee_id')->nullable()->comment('رئيس القسم');
            $table->timestamps();
            $table->softDeletes();

            
            $table->unique(['department_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
