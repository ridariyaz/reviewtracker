<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('gamification_image_url')->nullable()->after('gamification_reward');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('force_next_win')->default(false)->after('bad_count');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('gamification_image_url');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('force_next_win');
        });
    }
};
