<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ms_team_channels', function (Blueprint $table) {
            // Which learning mode this channel belongs to
            // e.g. 'Face-to-Face', 'Flexible Online Learning - 1st Shift', 'Flexible Online Learning - 2nd Shift', 'all'
            $table->string('learning_mode_filter', 60)->default('all')->after('gender_filter');
        });
    }

    public function down(): void
    {
        Schema::table('ms_team_channels', function (Blueprint $table) {
            $table->dropColumn('learning_mode_filter');
        });
    }
};
