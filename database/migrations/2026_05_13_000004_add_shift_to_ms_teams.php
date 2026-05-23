<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ms_teams', function (Blueprint $table) {
            // null = Face-to-Face (no shift), '1st Shift' or '2nd Shift' for Flexible Online
            $table->string('shift', 20)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('ms_teams', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
};
