<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('student_number', 20)->unique();
                $table->string('school_email')->unique()->nullable();
                $table->string('temp_password')->nullable();
                $table->string('grade_level', 50);
                $table->string('school_year', 20)->default('2026-2027');
                $table->string('section', 100)->nullable();
                $table->string('student_id_url')->nullable();
                $table->timestamp('credentials_sent_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('grade_level', 50);
                $table->string('learning_mode', 60);
                $table->string('shift', 20)->nullable();
                $table->enum('gender', ['male', 'female']);
                $table->string('school_year', 20)->default('2026-2027');
                $table->string('ms_team_id', 255)->nullable()->unique();
                $table->string('ms_team_url', 500)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('section_subjects')) {
            Schema::create('section_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_id')->constrained()->onDelete('cascade');
                $table->string('subject_name', 255);
                $table->string('teacher_name', 255)->nullable();
                $table->string('ms_channel_id', 255)->nullable();
                $table->string('schedule', 255)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_sections')) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sections');
        Schema::dropIfExists('section_subjects');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('students');
    }
};
