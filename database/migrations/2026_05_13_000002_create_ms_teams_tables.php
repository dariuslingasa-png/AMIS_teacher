<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_teams', function (Blueprint $table) {
            $table->id();
            $table->string('ms_team_id')->unique();           // Azure AD Team ID
            $table->string('display_name');
            $table->enum('type', ['grade', 'subject']);
            $table->string('grade_level', 50)->nullable();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('school_year', 20)->default('2026-2027');
            $table->string('team_url', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('ms_team_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ms_team_id_fk')->constrained('ms_teams')->onDelete('cascade');
            $table->string('ms_channel_id')->unique();        // Azure AD Channel ID
            $table->string('display_name');
            $table->enum('gender_filter', ['male', 'female', 'all'])->default('all');
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });

        Schema::create('student_ms_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('ms_team_id_fk')->constrained('ms_teams')->onDelete('cascade');
            $table->foreignId('ms_channel_id_fk')->nullable()->constrained('ms_team_channels')->nullOnDelete();
            $table->timestamp('enrolled_at')->nullable();
            $table->enum('status', ['pending', 'enrolled', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_ms_teams');
        Schema::dropIfExists('ms_team_channels');
        Schema::dropIfExists('ms_teams');
    }
};
