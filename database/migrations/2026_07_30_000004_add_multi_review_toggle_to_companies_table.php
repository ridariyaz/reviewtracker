<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('enable_multi_review_prompt')->default(false)->after('custom_links');
            $table->string('notification_email', 255)->nullable()->after('enable_multi_review_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['enable_multi_review_prompt', 'notification_email']);
        });
    }
};
