<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_requests', function (Blueprint $table): void {
            $table->index(['employee_id', 'status', 'created_at'], 'employee_requests_scope_status_created_idx');
            $table->index(['status', 'created_at'], 'employee_requests_status_created_idx');
        });

        Schema::table('performance_evaluations', function (Blueprint $table): void {
            $table->index(['employee_id', 'evaluation_date', 'id'], 'performance_employee_date_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('performance_evaluations', function (Blueprint $table): void {
            $table->dropIndex('performance_employee_date_id_idx');
        });

        Schema::table('employee_requests', function (Blueprint $table): void {
            $table->dropIndex('employee_requests_scope_status_created_idx');
            $table->dropIndex('employee_requests_status_created_idx');
        });
    }
};
