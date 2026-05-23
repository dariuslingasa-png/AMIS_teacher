<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Drop the old composite index that includes school_year
            $table->dropIndex('sections_lookup_idx');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('school_year');
            // Recreate index without school_year
            $table->index(['grade_level', 'learning_mode', 'shift', 'gender'], 'sections_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex('sections_lookup_idx');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->string('school_year', 20)->default('2026-2027');
            $table->index(['grade_level', 'learning_mode', 'shift', 'gender', 'school_year'], 'sections_lookup_idx');
        });
    }
};
