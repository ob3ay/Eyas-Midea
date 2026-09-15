<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            Schema::table('departments', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->after('description');
            });

            Schema::table('sections', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->after('section_head_employee_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            Schema::table('sections', function (Blueprint $table): void {
                $table->dropColumn('is_active');
            });

            Schema::table('departments', function (Blueprint $table): void {
                $table->dropColumn('is_active');
            });
        });
    }
};
