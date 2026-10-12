<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_attachments', function (Blueprint $table): void {
            $table->string('disk', 30)->default('public')->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('project_attachments', function (Blueprint $table): void {
            $table->dropColumn('disk');
        });
    }
};
