<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->string('grade_level', 50);
            $table->string('school_year', 20)->default('2026-2027');
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade_level', 50);
            $table->string('school_year', 20)->default('2026-2027');
            $table->timestamps();
        });

        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('school_year', 20)->default('2026-2027');
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'school_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('subjects');
    }
};
