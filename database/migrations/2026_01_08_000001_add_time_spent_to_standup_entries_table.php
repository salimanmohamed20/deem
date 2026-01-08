<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standup_entries', function (Blueprint $table) {
            $table->integer('time_spent')->nullable()->after('blockers')->comment('Time spent in minutes');
        });
    }

    public function down(): void
    {
        Schema::table('standup_entries', function (Blueprint $table) {
            $table->dropColumn('time_spent');
        });
    }
};
