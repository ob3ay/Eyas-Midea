<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CURRENT_STATUSES = ['draft', 'in_progress', 'in_review', 'completed', 'on_hold', 'cancelled'];

    public function up(): void
    {
        // Keep the existing rows and foreign keys. A temporary string column type lets us
        // translate legacy enum values before constraining the column to the new enum.
        Schema::table('projects', function (Blueprint $table): void {
            $table->unsignedBigInteger('department_id')->nullable()->change();
            $table->unsignedBigInteger('owner_employee_id')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->string('status', 30)->default('in_progress')->change();
        });

        DB::table('projects')->where('status', 'Draft')->update(['status' => 'draft']);
        DB::table('projects')->where('status', 'Active')->update(['status' => 'in_progress']);
        DB::table('projects')->where('status', 'On Hold')->update(['status' => 'on_hold']);
        DB::table('projects')->where('status', 'Completed')->update(['status' => 'completed']);
        DB::table('projects')->where('status', 'Cancelled')->update(['status' => 'cancelled']);

        Schema::table('projects', function (Blueprint $table): void {
            $table->enum('status', self::CURRENT_STATUSES)->default('in_progress')->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('status', 30)->default('in_progress')->change();
        });

        DB::table('projects')->where('status', 'draft')->update(['status' => 'Draft']);
        DB::table('projects')->where('status', 'in_progress')->update(['status' => 'Active']);
        DB::table('projects')->where('status', 'in_review')->update(['status' => 'Active']);
        DB::table('projects')->where('status', 'completed')->update(['status' => 'Completed']);
        DB::table('projects')->whereIn('status', ['on_hold', 'cancelled'])->update(['status' => 'On Hold']);

        Schema::table('projects', function (Blueprint $table): void {
            $table->enum('status', ['Draft', 'Active', 'On Hold', 'Completed', 'Cancelled'])
                ->default('Draft')
                ->change();
        });
    }
};
