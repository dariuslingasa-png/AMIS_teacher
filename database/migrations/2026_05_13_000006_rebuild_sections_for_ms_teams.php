<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old sections table and rebuild with full structure
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('sections');

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();                    // Islamic name, can be unnamed
            $table->string('grade_level', 50);
            $table->string('learning_mode', 60);                   // Face-to-Face | Flexible Online Learning
            $table->string('shift', 20)->nullable();               // 1st Shift | 2nd Shift | null for F2F
            $table->enum('gender', ['male', 'female']);
            $table->string('school_year', 20)->default('2026-2027');
            $table->string('ms_team_id', 255)->nullable()->unique(); // Azure AD Team ID
            $table->string('ms_team_url', 500)->nullable();
            $table->timestamps();

            $table->index(['grade_level', 'learning_mode', 'shift', 'gender', 'school_year'], 'sections_lookup_idx');
        });

        // section_subjects: each subject = one private channel in the section's team
        Schema::create('section_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('subject_name', 255);                   // e.g. Qur'an, Mathematics
            $table->string('teacher_name', 255)->nullable();       // e.g. Ust. Raffy
            $table->string('ms_channel_id', 255)->nullable();      // Azure AD Channel ID
            $table->timestamps();
        });

        // student_sections: tracks which section a student belongs to + MS enrollment status
        Schema::create('student_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->timestamp('ms_enrolled_at')->nullable();
            $table->enum('ms_status', ['pending', 'enrolled', 'failed'])->default('pending');
            $table->timestamps();

            $table->unique(['student_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sections');
        Schema::dropIfExists('section_subjects');
        Schema::dropIfExists('sections');
    }
};
