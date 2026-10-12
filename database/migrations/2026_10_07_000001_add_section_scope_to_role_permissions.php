<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_permissions', function (Blueprint $table): void {
            $table->enum('scope', ['self', 'section', 'department', 'organization'])->change();
        });
    }

    public function down(): void
    {
        DB::table('role_permissions')->where('scope', 'section')->update(['scope' => 'department']);

        Schema::table('role_permissions', function (Blueprint $table): void {
            $table->enum('scope', ['self', 'department', 'organization'])->change();
        });
    }
};
