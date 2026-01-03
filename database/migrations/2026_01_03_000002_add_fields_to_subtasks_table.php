<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->date('due_date')->nullable()->after('sort_order');
            $table->foreignId('assigned_to')->nullable()->after('due_date')->constrained('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['description', 'due_date', 'assigned_to']);
        });
    }
};
