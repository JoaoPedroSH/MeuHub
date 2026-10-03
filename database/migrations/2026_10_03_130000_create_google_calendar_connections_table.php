<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('google_calendar_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->dateTime('token_expires_at')->nullable();
            $table->string('google_email')->nullable();
            $table->string('calendar_id')->default('primary');
            $table->timestamps();
        });

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->string('source')->default('local')->after('color');
            $table->string('google_event_id')->nullable()->after('source');
            $table->unique(['user_id', 'google_event_id']);
        });
    }

    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'google_event_id']);
            $table->dropColumn(['source', 'google_event_id']);
        });
        Schema::dropIfExists('google_calendar_connections');
    }
};
