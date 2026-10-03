<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->jsonb('dashboard_shortcuts')->nullable()->after('is_admin');
            $table->jsonb('admin_shortcuts')->nullable()->after('dashboard_shortcuts');
        });
    }
    public function down(): void { Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['dashboard_shortcuts', 'admin_shortcuts'])); }
};
