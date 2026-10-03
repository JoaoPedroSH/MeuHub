<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->jsonb('tag_snapshots')->nullable()->after('color');
            $table->dateTime('starts_at')->nullable()->after('tag_snapshots');
            $table->dateTime('ends_at')->nullable()->after('starts_at');
            $table->boolean('all_day')->default(false)->after('ends_at');
            $table->string('calendar_color', 20)->default('#6366f1')->after('all_day');
        });
    }
    public function down(): void
    {
        Schema::table('notes', fn (Blueprint $table) => $table->dropColumn(['tag_snapshots', 'starts_at', 'ends_at', 'all_day', 'calendar_color']));
    }
};
