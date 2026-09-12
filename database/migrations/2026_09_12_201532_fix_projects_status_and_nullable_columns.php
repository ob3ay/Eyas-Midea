<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['owner_employee_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'department_id', 'owner_employee_id', 'end_date']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('description')
                ->constrained('departments')->nullOnDelete();

            $table->foreignId('owner_employee_id')->nullable()->after('department_id')
                ->constrained('employees')->nullOnDelete();

            $table->date('end_date')->nullable()->after('start_date');

            $table->enum('status', ['in_progress', 'in_review', 'completed', 'on_hold'])
                ->default('in_progress')->after('actual_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['owner_employee_id']);
            $table->dropColumn(['status', 'department_id', 'owner_employee_id', 'end_date']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('department_id')->after('description')->constrained('departments')->restrictOnDelete();
            $table->foreignId('owner_employee_id')->after('department_id')->constrained('employees')->restrictOnDelete();
            $table->date('end_date')->after('start_date');
            $table->enum('status', ['Draft', 'Active', 'On Hold', 'Completed', 'Cancelled'])->default('Draft')->after('actual_end_date');
        });
    }
};
